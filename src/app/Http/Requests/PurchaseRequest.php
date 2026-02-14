<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'payment_method'   => ['required', 'in:1,2'],
            'shipping_address' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required'   => '支払い方法を選択してください。',
            'payment_method.in'         => '正しい支払い方法を選択してください。',
            'shipping_address.required' => '配送先を選択してください。',
        ];
    }

    /**
     * バリデーション後に old() で値を復元できるよう withInput を有効にする
     */
    protected function getRedirectUrl()
    {
        return $this->redirector->getUrlGenerator()->previous();
    }
}
