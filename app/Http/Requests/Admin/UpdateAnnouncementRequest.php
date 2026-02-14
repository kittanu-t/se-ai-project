<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }

    public function rules(): array
    {
        return [
            'title'        => ['required','string','max:200'],
            'content'      => ['required','string'],
            'audience'     => ['required','in:all,users,staff'],
            'published_at' => ['nullable','date'],
        ];
    }
}