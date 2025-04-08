<?php

namespace App\Http\Requests\MasterData\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UnitStoreRequest extends FormRequest
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
            'nama_satuan' => [
                'required',
                'string',
                'max:30',
                $id ? "unique:units,nama_satuan,$id,id" : "unique:units,nama_satuan",
            ],
        ];
    }
}
