<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * いいね追加
     */
    public function store($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 重複防止: まだいいねしていない場合のみ作成
        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
        ]);

        return redirect()->route('items.show', ['item_id' => $item->id]);
    }

    /**
     * いいね解除
     */
    public function destroy($item_id)
    {
        $item = Item::findOrFail($item_id);

        Favorite::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->delete();

        return redirect()->route('items.show', ['item_id' => $item->id]);
    }
}
