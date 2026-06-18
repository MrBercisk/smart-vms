<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitorRequest extends FormRequest
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
            'full_name'       => 'required|string|max:100',
            'company_name'    => 'nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email',
            'identity_number' => 'required|string|max:50',
            'identity_type'   => 'required|in:KTP,SIM,Passport',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'          => 'sometimes|in:active,blacklist',
        ];
    }
}
