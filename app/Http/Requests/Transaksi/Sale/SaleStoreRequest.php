<?php

namespace App\Http\Requests\Transaksi\Sale;

use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no_invoice' => ['required', 'string', 'max:20', 'unique:sales,no_invoice'],
            'metode_penjualan' => ['required', 'in:' . implode(',', array_keys(Sale::metodePenjualan()))],
            'metode_pembayaran' => ['required', 'in:' . implode(',', array_keys(Sale::metodePembayaran()))],
            'paid_amount' => ['required', 'numeric'],
            'items' => ['required', 'array'],
            'items.*.id' => ['required_with:items', 'exists:products,id'],
            'items.*.satuan_cart' => ['required_with:items', 'exists:units,id'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:1'],
            'items.*.harga_jual' => ['required_with:items', 'numeric'],
        ];
    }
}
