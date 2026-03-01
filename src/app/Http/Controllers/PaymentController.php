<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PaymentController extends Controller
{
    /**
     * Stripe Checkout セッションを作成してリダイレクト
     */
    public function checkout(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // 売却済みチェック
        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('error', 'この商品は売り切れです。');
        }

        // 配送先バリデーション
        $profile = Auth::user()->profile;
        $shippingAddress = session("shipping_address_{$item_id}", [
            'zipcode' => $profile->zipcode ?? '',
            'address' => $profile->address ?? '',
            'building' => $profile->building ?? '',
        ]);

        if (empty($shippingAddress['zipcode']) || empty($shippingAddress['address'])) {
            return back()->withErrors(['shipping' => '配送先住所を登録してください。']);
        }

        $paymentMethod = $request->input('payment_method');

        // Stripe APIキーの設定チェック
        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            Log::error('Stripe secret key is not configured.');
            return back()->withErrors(['stripe' => '決済サービスの設定が完了していません。管理者にお問い合わせください。']);
        }

        try {
            Stripe::setApiKey($stripeSecret);

            // 支払い方法に応じた payment_method_types
            $paymentMethodTypes = $paymentMethod === '1'
                ? ['konbini']   // コンビニ払い
                : ['card'];     // カード払い

            $session = StripeSession::create([
                'payment_method_types' => $paymentMethodTypes,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['item_id' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.show', ['item_id' => $item->id]),
                'metadata' => [
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'payment_method' => $paymentMethod,
                ],
            ]);

            return redirect($session->url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error: ' . $e->getMessage());
            return back()->withErrors(['stripe' => '決済処理中にエラーが発生しました。しばらくしてから再度お試しください。']);
        }
    }

    /**
     * Stripe Checkout 成功後のコールバック
     */
    public function success(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // 既に売却済みなら二重処理防止
        if ($item->is_sold) {
            return redirect()->route('items.index')
                ->with('info', 'この商品は既に購入済みです。');
        }

        // Stripe セッション検証
        $sessionId = $request->query('session_id');
        if ($sessionId) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status === 'paid' || $session->payment_status === 'unpaid') {
                // 配送先情報（セッション → プロフィール）
                $profile = Auth::user()->profile;
                $shippingAddress = session("shipping_address_{$item_id}", [
                    'zipcode' => $profile->zipcode ?? '',
                    'address' => $profile->address ?? '',
                    'building' => $profile->building,
                ]);

                // 購入レコード作成（住所スナップショット）
                Purchase::create([
                    'user_id' => Auth::id(),
                    'item_id' => $item->id,
                    'payment_method' => $session->metadata->payment_method,
                    'zipcode' => $shippingAddress['zipcode'],
                    'address' => $shippingAddress['address'],
                    'building' => $shippingAddress['building'],
                ]);

                // 商品を売却済みに更新
                $item->update(['is_sold' => true]);

                // セッションの一時配送先をクリア
                session()->forget("shipping_address_{$item_id}");

                return redirect()->route('items.index')
                    ->with('success', '購入が完了しました。');
            }
        }

        return redirect()->route('purchase.show', ['item_id' => $item->id])
            ->with('error', '決済に失敗しました。もう一度お試しください。');
    }
}
