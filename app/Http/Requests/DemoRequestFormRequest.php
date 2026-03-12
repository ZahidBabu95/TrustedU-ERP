<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemoRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_name'      => 'required|string|max:150',
            'email'             => 'required|email|max:150',
            'phone'             => 'required|string|max:30',
            'institution_name'  => 'required|string|max:200',
            'institution_type'  => 'required|in:school,college,university,madrasha,other',
            'district'          => 'nullable|string|max:100',
            'student_count'     => 'nullable|string|max:50',
            'interested_modules'=> 'nullable|array',
            'preferred_date'    => 'nullable|date|after:today',
            'notes'             => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_name.required'     => 'যোগাযোগকারীর নাম লিখুন।',
            'email.required'            => 'ইমেইল ঠিকানা লিখুন।',
            'phone.required'            => 'মোবাইল নম্বর লিখুন।',
            'institution_name.required' => 'প্রতিষ্ঠানের নাম লিখুন।',
            'institution_type.required' => 'প্রতিষ্ঠানের ধরন নির্বাচন করুন।',
        ];
    }
}
