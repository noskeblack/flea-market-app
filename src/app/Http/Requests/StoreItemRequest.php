<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreItemRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name'         => ['required', 'string'],
            'description'  => ['required', 'string', 'max:255'],
            'image'        => ['required', 'image', 'mimes:jpeg,png'],
            'categories'   => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'brand'        => ['nullable', 'string', 'max:255'],
            'price'        => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => '商品名は必須です。',
            'description.required'  => '商品の説明は必須です。',
            'description.max'       => '商品の説明は255文字以内で入力してください。',
            'image.required'        => '商品画像は必須です。',
            'image.image'           => '商品画像は画像ファイルを選択してください。',
            'image.mimes'           => '商品画像はjpegもしくはpng形式でアップロードしてください。',
            'categories.required'   => 'カテゴリーを1つ以上選択してください。',
            'categories.min'        => 'カテゴリーを1つ以上選択してください。',
            'condition_id.required' => '商品の状態を選択してください。',
            'condition_id.exists'   => '正しい商品の状態を選択してください。',
            'price.required'        => '販売価格は必須です。',
            'price.numeric'         => '販売価格は数値で入力してください。',
            'price.min'             => '販売価格は0円以上で入力してください。',
        ];
    }
}
