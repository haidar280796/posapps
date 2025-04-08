<?php

namespace App\Http\Requests\MasterData\ProductPrice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductPriceStoreRequest extends FormRequest
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
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'satuan_id' => ['required', 'exists:units,id'],
            'konversi' => ['required', 'numeric', 'min:1'],
            'harga_beli' => ['nullable', 'numeric', 'min:1'],
            'harga_jual' => ['required', 'numeric', 'min:1'],
            'barcode' => ['nullable', 'string'],
        ];
    }
}
