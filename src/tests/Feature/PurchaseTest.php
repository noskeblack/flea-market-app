<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト共通のセットアップ
     * 各テスト実行前にマスターデータ（conditions）を投入する
     */
    protected function setUp(): void
    {
        parent::setUp();

        // マスターデータ: 商品の状態
        Condition::create(['name' => '良好']);
        Condition::create(['name' => '目立った傷や汚れなし']);
        Condition::create(['name' => 'やや傷や汚れあり']);
        Condition::create(['name' => '状態が悪い']);
    }

    /**
     * 購入実行時、purchases テーブルに「購入時の住所」が正しく保存されているか
     * （住所スナップショット機能の確認）
     */
    public function test_purchase_stores_address_snapshot()
    {
        // 購入者とプロフィール（住所情報）を作成
        $buyer = User::factory()->create(['name' => '購入者']);
        $profile = Profile::factory()->create([
            'user_id' => $buyer->id,
            'zipcode' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => '皇居ビル101',
        ]);

        // 出品者と商品を作成
        $seller = User::factory()->create(['name' => '出品者']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 5000,
            'is_sold' => false,
        ]);

        // Stripe セッションのモックを作成
        $mockSession = Mockery::mock('alias:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->andReturn((object) [
                'payment_status' => 'paid',
                'metadata' => (object) [
                    'payment_method' => '2', // カード払い
                ],
            ]);

        // Stripe APIキー設定のモック
        Stripe::setApiKey('sk_test_dummy');

        // ログインして決済成功コールバックを実行
        $response = $this->actingAs($buyer)
            ->get(route('payment.success', [
                'item_id' => $item->id,
                'session_id' => 'cs_test_dummy_session_id',
            ]));

        // リダイレクトされることを確認
        $response->assertRedirect(route('items.index'));

        // purchases テーブルに購入時の住所が正しく保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 2,
            'zipcode' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => '皇居ビル101',
        ]);

        // 住所スナップショットの確認：プロフィールの住所を変更しても購入レコードは変わらない
        $profile->update([
            'zipcode' => '530-0001',
            'address' => '大阪府大阪市北区梅田1-1',
            'building' => '梅田ビル202',
        ]);

        // 購入レコードには元の住所が保存されたままであることを確認
        $purchase = Purchase::where('item_id', $item->id)->first();
        $this->assertEquals('100-0001', $purchase->zipcode);
        $this->assertEquals('東京都千代田区千代田1-1', $purchase->address);
        $this->assertEquals('皇居ビル101', $purchase->building);
    }

    /**
     * 決済完了後、商品のステータスが is_sold = true に更新されるか
     */
    public function test_purchase_updates_item_to_sold()
    {
        // 購入者とプロフィールを作成
        $buyer = User::factory()->create(['name' => '購入者']);
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'zipcode' => '150-0002',
            'address' => '東京都渋谷区渋谷2-2',
            'building' => '渋谷タワー303',
        ]);

        // 出品者と商品を作成（未売却状態）
        $seller = User::factory()->create(['name' => '出品者']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '売却テスト商品',
            'price' => 10000,
            'is_sold' => false,
        ]);

        // 購入前は未売却であることを確認
        $this->assertFalse($item->fresh()->is_sold);

        // Stripe セッションのモックを作成
        $mockSession = Mockery::mock('alias:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->andReturn((object) [
                'payment_status' => 'paid',
                'metadata' => (object) [
                    'payment_method' => '2', // カード払い
                ],
            ]);

        // Stripe APIキー設定のモック
        Stripe::setApiKey('sk_test_dummy');

        // ログインして決済成功コールバックを実行
        $response = $this->actingAs($buyer)
            ->get(route('payment.success', [
                'item_id' => $item->id,
                'session_id' => 'cs_test_dummy_session_id',
            ]));

        // リダイレクトされることを確認
        $response->assertRedirect(route('items.index'));

        // 商品のステータスが is_sold = true に更新されていることを確認
        $this->assertTrue($item->fresh()->is_sold);

        // データベースでも確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }
}
