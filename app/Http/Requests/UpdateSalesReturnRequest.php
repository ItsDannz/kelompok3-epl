<?php

namespace App\Http\Requests;

// RF-015: Satukan aturan validasi retur.

use App\Http\Requests\Rules\SalesReturnRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return SalesReturnRules::all();
    }
}
