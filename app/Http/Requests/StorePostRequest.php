<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required_if:type,text|string|max:255',
            'content' => 'required_if:type,text|string',
            'type' => 'nullable|in:text,image,video',
            'description' => 'nullable|string|max:500',
            'video_url'   => 'nullable|array|min:1',
            'video_url.*' => 'mimetypes:video/mp4,video/avi,video/mov|max:40480',
            'image_url'   => 'nullable|array|min:1',
            'image_url.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        ];
    }
}
