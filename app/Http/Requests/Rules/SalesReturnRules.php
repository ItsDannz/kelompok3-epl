<?php

namespace App\Http\Requests\Rules;

// RF-015: Satukan aturan validasi retur.

final class SalesReturnRules
{
    public static function all(): array
    {
        return [
            'return_type' => ['required', 'in:refund_cash,store_credit'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.transaction_detail_id' => ['required', 'integer', 'exists:transaction_details,id'],
            'items.*.qty_return' => ['nullable', 'integer', 'min:0'],
            'items.*.return_reason' => ['nullable', 'string', 'max:255'],
            'items.*.restock_to_inventory' => ['nullable', 'boolean'],
        ];
    }
}
