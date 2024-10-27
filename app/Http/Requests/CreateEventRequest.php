<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|min:5|max:50',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:5048|',
            'pass_event' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5048|',
        ];
        return $rules;
    }
}
