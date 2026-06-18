<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'visitor_id'     => 'required|exists:visitors,id',
            'employee_id'    => 'required|exists:employees,id',
            'department_id'  => 'required|exists:departments,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'purpose'        => 'required|string',
            'visitor_photo'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'identity_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
