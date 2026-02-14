<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * コメント投稿処理
     */
    public function store(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('items.show', ['item_id' => $item->id]);
    }

    /**
     * コメント削除処理
     */
    public function destroy($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        // コメント投稿者のみ削除可能
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $itemId = $comment->item_id;
        $comment->delete();

        return redirect()->route('items.show', ['item_id' => $itemId]);
    }
}
