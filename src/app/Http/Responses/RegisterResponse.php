<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * 会員登録後のレスポンスを生成
     *
     * 登録後はメール認証誘導画面へリダイレクト
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false], 201)
            : redirect()->route('verification.notice');
    }
}
