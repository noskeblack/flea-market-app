<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * 配送先住所変更画面
     */
    public function edit($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 現在の配送先（セッション → プロフィール）
        $profile = Profile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['zipcode' => null, 'address' => null, 'building' => null]
        );

        $shippingAddress = session("shipping_address_{$item_id}", [
            'zipcode' => $profile->zipcode,
            'address' => $profile->address,
            'building' => $profile->building,
        ]);

        return view('addresses.edit', compact('item', 'shippingAddress'));
    }

    /**
     * 配送先住所更新処理
     */
    public function update(AddressRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // セッションに一時的な配送先を保存（購入確定時にスナップショットとして保存される）
        session(["shipping_address_{$item_id}" => [
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]]);

        // プロフィールも同時に更新
        $profile = Auth::user()->profile;
        if ($profile) {
            $profile->update($request->only('zipcode', 'address', 'building'));
        } else {
            Profile::create(array_merge(
                ['user_id' => Auth::id()],
                $request->only('zipcode', 'address', 'building')
            ));
        }

        return redirect()->route('purchase.show', ['item_id' => $item->id])
            ->with('success', '配送先を変更しました。');
    }
}
