<?php

namespace Tests\Feature\Refactoring;

use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// RF-011: Ekstraksi penentuan bucket aging, termasuk batas pecahan hari Carbon.
class DebtAgingCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_both_models_preserve_paid_null_due_and_exact_time_boundaries(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 16)->startOfDay());
        foreach ([Receivable::class, Payable::class] as $class) {
            foreach ([['paid', null, 'paid'], ['paid', now()->subDays(95), 'paid'],
                ['unpaid', null, 'no_due_date'], ['unpaid', now()->addDay(), 'current'],
                ['unpaid', now(), 'current'], ['partial', now()->subDay(), '0-30'],
                ['overdue', now()->subDays(95), '90+']] as [$status, $due, $expected]) {
                $this->assertSame($expected, (new $class(['status' => $status, 'due_date' => $due]))->aging_bucket);
            }
            foreach ([30 => ['0-30', '31-60'], 60 => ['31-60', '61-90'], 90 => ['61-90', '90+']] as $days => [$at, $after]) {
                foreach ([-1, 0, 1] as $seconds) {
                    $this->travelTo(now()->setDate(2026, 11, 16)->startOfDay()->addSeconds($seconds));
                    $model = new $class(['status' => 'unpaid', 'due_date' => '2026-11-16']);
                    $model->due_date = now()->setDate(2026, 11, 16)->startOfDay()->subDays($days);
                    $this->assertSame($seconds <= 0 ? $at : $after, $model->aging_bucket, $class.' '.$days.' days '.$seconds.' seconds');
                }
            }
            $this->travelTo(now()->setDate(2026, 11, 16)->startOfDay());
        }
    }
}
