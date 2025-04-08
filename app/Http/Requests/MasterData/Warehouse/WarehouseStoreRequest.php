<?php

namespace App\Http\Requests\MasterData\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WarehouseStoreRequest extends FormRequest
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
            'nama_gudang' => ['required', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'tipe' => ['required', 'in:gudang,toko', 'string'],
        ];
    }
}
