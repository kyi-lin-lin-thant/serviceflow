<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatchServiceCategoryRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('service_categories', 'name')
                    ->ignore($this->route('serviceCategory')),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'status' => [
                'sometimes',
                'in:active,inactive',
            ],
        ];
    }
}
