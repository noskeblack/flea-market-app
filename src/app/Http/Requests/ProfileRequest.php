<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name'          => ['required', 'string', 'max:20'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png'],
            'zipcode'       => ['required', 'string', 'size:8', 'regex:/^\d{3}-\d{4}$/'],
            'address'       => ['required', 'string'],
            'building'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'          => 'ユーザー名は必須です。',
            'name.max'               => 'ユーザー名は20文字以内で入力してください。',
            'profile_image.image'    => 'プロフィール画像は画像ファイルを選択してください。',
            'profile_image.mimes'    => 'プロフィール画像はjpegもしくはpng形式でアップロードしてください。',
            'zipcode.required'       => '郵便番号は必須です。',
            'zipcode.size'           => '郵便番号はハイフンありの8文字で入力してください。',
            'zipcode.regex'          => '郵便番号はハイフンありの正しい形式（例: 123-4567）で入力してください。',
            'address.required'       => '住所は必須です。',
            'building.max'           => '建物名は255文字以内で入力してください。',
        ];
    }
}
