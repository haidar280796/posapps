<?php

namespace App\Http\Requests\MasterData\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductStoreRequest extends FormRequest
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
        // Ambil ID dari route (jika ada)
        $id = $this->route('id'); // Sesuaikan dengan nama parameter di route

        return [
            'kode_produk' => [
                'required',
                'string',
                'max:50',
                $id ? "unique:products,kode_produk,$id,id" : "unique:products,kode_produk",
            ],
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'satuan_dasar_id' => ['required', 'exists:units,id'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'harga_beli' => ['nullable', 'numeric', 'min:1'],
            'harga_jual' => ['nullable', 'numeric', 'gte:harga_beli'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
