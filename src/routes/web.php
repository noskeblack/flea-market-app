<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 公開ルート（認証不要） ---
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// --- 認証＋メール認証必須ルート ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 商品購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // Stripe 決済
    Route::post('/purchase/{item_id}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/purchase/{item_id}/success', [PaymentController::class, 'success'])->name('payment.success');

    // 配送先住所変更
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

    // コメント
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comment/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // いいね（お気に入り）
    Route::post('/item/{item_id}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/item/{item_id}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // マイページ
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('mypage.update');
});
