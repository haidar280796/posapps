<?php

namespace App\Http\Requests\Transaksi\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockStoreRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'satuan_id' => [
                'required',
                Rule::exists('product_units', 'satuan_id')
                ->where(function ($query) {
                    if ($this->filled('product_id')) { // Pastikan product_id tersedia
                        $query->where('product_id', $this->input('product_id'));
                    }
                }),
            ],
        ];
    }
}
