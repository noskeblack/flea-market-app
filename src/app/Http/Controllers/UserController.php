<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * マイページ表示
     */
    public function show()
    {
        $user = Auth::user();
        $tab = request('tab', 'sell');

        if ($tab === 'buy') {
            // 購入した商品
            $items = Item::whereHas('purchases', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('created_at', 'desc')->get();
        } else {
            // 出品した商品
            $items = Item::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('users.show', compact('user', 'items', 'tab'));
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['zipcode' => null, 'address' => null, 'building' => null]
        );

        return view('users.edit', compact('user', 'profile'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // ユーザー名の更新
        $user->name = $request->input('name');

        // プロフィール画像のアップロード
        if ($request->hasFile('profile_image')) {
            // 古い画像があれば削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        // プロフィール（住所情報）の更新
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['zipcode' => null, 'address' => null, 'building' => null]
        );

        $profile->update([
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        return redirect()->route('mypage.show')->with('success', 'プロフィールを更新しました。');
    }
}
