<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'আপনার নাম লিখুন।',
            'email.required'   => 'ইমেইল ঠিকানা লিখুন।',
            'email.email'      => 'সঠিক ইমেইল ঠিকানা লিখুন।',
            'message.required' => 'বার্তা লিখুন।',
            'message.min'      => 'বার্তা কমপক্ষে ১০ অক্ষরের হতে হবে।',
        ];
    }
}
