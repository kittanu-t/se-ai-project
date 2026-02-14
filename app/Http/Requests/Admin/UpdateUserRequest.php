<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }

    public function rules(): array
    {
        $id = $this->route('user'); // จาก resource route {user}
        return [
            'name'   => ['required','string','max:100'],
            'email'  => ['required','email','max:150', Rule::unique('users','email')->ignore($id)],
            'role'   => ['required','in:admin,staff,user'],
            'phone'  => ['nullable','string','max:30'],
            'active' => ['required','boolean'],
            'password' => ['nullable','string','min:8','confirmed'],
        ];
    }
}