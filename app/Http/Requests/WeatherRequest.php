<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:32'], // 例: "Kofu,JP" や "Tokyo,JP"
        ];
    }

    public function messages(): array
    {
        return [
            'city.required' => '都市名を入力してください（例: Tokyo,JP / Kofu,JP）。',
            'city.max'      => '都市名が長すぎます。',
        ];
    }
}
