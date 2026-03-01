<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 購入画面表示
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 売却済みチェック
        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', 'この商品は売り切れです。');
        }

        // ユーザーのプロフィール（配送先）を取得
        $profile = Profile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['zipcode' => null, 'address' => null, 'building' => null]
        );

        // セッションに一時的な配送先があればそちらを優先
        $shippingAddress = session("shipping_address_{$item_id}", [
            'zipcode' => $profile->zipcode,
            'address' => $profile->address,
            'building' => $profile->building,
        ]);

        return view('purchases.show', compact('item', 'shippingAddress'));
    }
}
