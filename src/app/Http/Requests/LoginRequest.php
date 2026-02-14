<?php

namespace App\Http\Requests;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    public function rules()
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => 'メールアドレスは必須です。',
            'email.email'       => 'メールアドレスは正しい形式で入力してください。',
            'password.required' => 'パスワードは必須です。',
        ];
    }
}
