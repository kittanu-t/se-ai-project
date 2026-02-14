<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }

    public function rules(): array
    {
        return [
            'name'   => ['required','string','max:120'],
            'sport_type' => ['required','string','max:60'],
            'location'   => ['required','string','max:200'],
            'capacity'   => ['required','integer','min:0'],
            'status'     => ['required','in:available,closed,maintenance'],
            'owner_id'   => ['nullable','integer','exists:users,id'],
            'min_duration_minutes' => ['required','integer','min:15'],
            'max_duration_minutes' => ['required','integer','gte:min_duration_minutes'],
            'lead_time_hours'      => ['required','integer','min:0'],
        ];
    }
}