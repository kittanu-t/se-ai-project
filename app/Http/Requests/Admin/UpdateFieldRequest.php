<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }
    public function rules(): array
    {
        return (new StoreFieldRequest)->rules();
    }
}