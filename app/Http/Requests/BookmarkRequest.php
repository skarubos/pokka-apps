<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string|max:32',
            'url' => 'required|url',
            'priority' => 'required|integer',
            'img_name' => 'nullable|mimes:jpeg,png,jpg|max:128',
        ];
    }

    public function messages(): array
    {
        return [
            'img_name.mimes' => '画像ファイルはjpeg, png, jpg形式である必要があります。',
            'img_name.max' => '画像ファイルのサイズは128KB以下である必要があります。',
        ];
    }
}
