<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }

    public function rules(): array
    {
        return [
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role'     => ['required','in:admin,staff,user'],
            'phone'    => ['nullable','string','max:30'],
            'active'   => ['required','boolean'],
        ];
    }
}