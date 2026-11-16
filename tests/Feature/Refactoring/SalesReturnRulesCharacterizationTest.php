<?php

namespace Tests\Feature\Refactoring;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Http\Requests\UpdateSalesReturnRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

// RF-015: Satukan aturan validasi retur; input yang diterima/ditolak harus tetap sama.
class SalesReturnRulesCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_update_keep_the_same_valid_and_invalid_input_contract(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'RF', 'image' => 'rf.png', 'description' => 'Test']);
        $product = Product::create(['image' => 'rf.png', 'barcode' => 'RF', 'sku' => 'RF', 'title' => 'RF',
            'category_id' => $category->id, 'buy_price' => 7000, 'sell_price' => 10000, 'stock' => 50, 'tax_rate' => 0]);
        $tx = Transaction::create(['invoice' => 'RF', 'cashier_id' => $user->id, 'cash' => 10000, 'change' => 0,
            'discount' => 0, 'grand_total' => 10000, 'payment_method' => 'cash', 'payment_status' => 'paid']);
        $detail = $tx->details()->create(['product_id' => $product->id, 'qty' => 1, 'price' => 10000]);
        $valid = ['return_type' => 'refund_cash', 'items' => [['transaction_detail_id' => $detail->id,
            'qty_return' => 0, 'return_reason' => null, 'restock_to_inventory' => true]]];

        foreach ([new StoreSalesReturnRequest, new UpdateSalesReturnRequest] as $request) {
            $this->assertTrue($request->authorize());
            $this->assertTrue(Validator::make($valid, $request->rules())->passes());
            $this->assertTrue(Validator::make([...$valid, 'return_type' => 'store_credit'], $request->rules())->passes());
            foreach (['qty_return' => -1, 'return_reason' => str_repeat('x', 256), 'restock_to_inventory' => 'yes', 'transaction_detail_id' => 999999] as $field => $value) {
                $input = $valid;
                $input['items'][0][$field] = $value;
                $this->assertTrue(Validator::make($input, $request->rules())->errors()->has('items.0.'.$field));
            }
            $this->assertTrue(Validator::make([...$valid, 'items' => []], $request->rules())->errors()->has('items'));
            $this->assertTrue(Validator::make([...$valid, 'return_type' => 'invalid'], $request->rules())->errors()->has('return_type'));
        }
    }
}
