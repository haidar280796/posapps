<?php

namespace App\Http\Requests\MasterData\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerStoreRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ];
    }
}
