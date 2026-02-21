<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * メール認証完了後のレスポンスを生成
     *
     * 初回認証後はプロフィール設定画面へリダイレクト
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['message' => 'Email verified.'])
            : redirect()->route('mypage.edit');
    }
}
