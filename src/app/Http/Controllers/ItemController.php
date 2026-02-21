<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧画面
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');
        $keyword = $request->input('keyword');
        $userId = Auth::id();

        if ($tab === 'mylist' && Auth::check()) {
            $query = Item::myList($userId);
        } else {
            $query = Item::recommended($userId);
        }

        $items = $query->keyword($keyword)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    /**
     * 商品詳細画面
     */
    public function show($item_id)
    {
        $item = Item::with(['user', 'condition', 'categories', 'comments.user', 'favorites'])->findOrFail($item_id);

        // いいね数・コメント数
        $favoritesCount = $item->favorites->count();
        $commentsCount = $item->comments->count();

        // ログインユーザーがいいね済みか
        $isFavorited = false;
        if (Auth::check()) {
            $isFavorited = $item->favorites->where('user_id', Auth::id())->isNotEmpty();
        }

        return view('items.show', compact('item', 'favoritesCount', 'commentsCount', 'isFavorited'));
    }

    /**
     * 商品出品画面
     */
    public function create()
    {
        $categories = Category::orderBy('id')->get();
        $conditions = Condition::orderBy('id')->get();

        return view('items.create', compact('categories', 'conditions'));
    }

    /**
     * 商品出品処理
     */
    public function store(StoreItemRequest $request)
    {
        // 商品画像の保存
        $imagePath = $request->file('image')->store('items', 'public');

        // 商品レコード作成
        $item = Item::create([
            'user_id'      => Auth::id(),
            'condition_id' => $request->input('condition_id'),
            'name'         => $request->input('name'),
            'brand'        => $request->input('brand'),
            'price'        => $request->input('price'),
            'description'  => $request->input('description'),
            'image'        => $imagePath,
        ]);

        // カテゴリーの紐付け（多対多）
        $item->categories()->attach($request->input('categories'));

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}
