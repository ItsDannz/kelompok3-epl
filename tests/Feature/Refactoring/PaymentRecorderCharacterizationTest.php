<?php

namespace Tests\Feature\Refactoring;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// RF-007: Ekstraksi pencatatan pembayaran piutang.
// RF-008: Ekstraksi pencatatan pembayaran hutang.
class PaymentRecorderCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    public static function channels(): array
    {
        return ['piutang web' => ['receivables', false], 'piutang API' => ['receivables', true],
            'hutang web' => ['payables', false], 'hutang API' => ['payables', true]];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);
        $user = User::where('email', 'arya@gmail.com')->firstOrFail();
        $user->markEmailAsVerified();
        $this->actingAs($user);
        Sanctum::actingAs($user, ['*']);
    }

    private function bill(string $kind): Receivable|Payable
    {
        $common = ['total' => 100000, 'paid' => 0, 'status' => 'unpaid', 'due_date' => now()->subDays(2)];
        if ($kind === 'receivables') {
            $customer = Customer::create(['name' => 'RF', 'no_telp' => '900001', 'address' => 'Uji']);
            $tx = Transaction::create(['invoice' => 'RF-TX', 'cashier_id' => auth()->id(), 'customer_id' => $customer->id,
                'grand_total' => 100000, 'cash' => 0, 'change' => 0, 'discount' => 0, 'payment_method' => 'credit', 'payment_status' => 'unpaid']);

            return Receivable::create([...$common, 'invoice' => 'RF-RCV', 'customer_id' => $customer->id, 'transaction_id' => $tx->id]);
        }
        $supplier = Supplier::create(['name' => 'RF Supplier']);

        return Payable::create([...$common, 'document_number' => 'RF-PAY', 'supplier_id' => $supplier->id]);
    }

    private function pay(string $kind, bool $api, int $id, array $payload)
    {
        return $api ? $this->postJson("/api/v1/{$kind}/{$id}/pay", $payload)
            : $this->post(route($kind.'.pay', $id), $payload);
    }

    private function payload(float $amount): array
    {
        return ['amount' => $amount, 'paid_at' => now()->toDateString(), 'method' => 'cash', 'note' => 'RF characterization'];
    }

    #[DataProvider('channels')]
    public function test_overdue_partial_then_exact_payment_preserves_balance_history_and_transaction_status(string $kind, bool $api): void
    {
        $bill = $this->bill($kind);
        $response = $this->pay($kind, $api, $bill->id, $this->payload(40000));
        $api ? $response->assertOk() : $response->assertSessionHas('success')->assertRedirect(route($kind.'.show', $bill));
        $this->assertSame('overdue', $bill->fresh()->status);
        $this->assertEquals(60000, $bill->fresh()->remaining);
        $this->pay($kind, $api, $bill->id, $this->payload(60000));
        $this->assertSame('paid', $bill->fresh()->status);
        $this->assertEquals(0, $bill->fresh()->remaining);
        $this->assertEquals(100000, $bill->payments()->sum('amount'));
        $this->assertSame(2, $bill->payments()->count());
        if ($bill instanceof Receivable) {
            $this->assertSame('paid', $bill->transaction->fresh()->payment_status);
        }
        $response = $this->pay($kind, $api, $bill->id, $this->payload(1));
        $api ? $response->assertUnprocessable()->assertJsonValidationErrors('amount') : $response->assertSessionHasErrors('amount');
        $this->assertSame(2, $bill->payments()->count());
    }

    #[DataProvider('channels')]
    public function test_inactive_bank_rejection_rolls_back_payment_and_balance(string $kind, bool $api): void
    {
        $bill = $this->bill($kind);
        $bank = BankAccount::create(['bank_name' => 'RF', 'account_number' => '900001', 'account_name' => 'RF', 'is_active' => false]);
        $this->pay($kind, $api, $bill->id, [...$this->payload(10000), 'bank_account_id' => $bank->id])->assertUnprocessable();
        $this->assertEquals(0, $bill->fresh()->paid);
        $this->assertSame('unpaid', $bill->fresh()->status);
        $this->assertSame(0, $bill->payments()->count());
    }
}
