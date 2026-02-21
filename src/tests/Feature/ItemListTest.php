<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemListTest extends TestCase
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
     * recommended スコープ：ログイン中、自分の出品した商品が表示されないこと
     */
    public function test_recommended_scope_excludes_own_items()
    {
        // ログインユーザーと他のユーザーを作成
        $loginUser = User::factory()->create(['name' => 'テストユーザー1']);
        $otherUser = User::factory()->create(['name' => 'テストユーザー2']);

        // ログインユーザーの出品商品
        $ownItem = Item::factory()->create([
            'user_id' => $loginUser->id,
            'name' => '自分の商品',
        ]);

        // 他ユーザーの出品商品
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);

        // ログインして商品一覧（おすすめタブ）を表示
        $response = $this->actingAs($loginUser)
            ->get(route('items.index', ['tab' => 'recommend']));

        $response->assertStatus(200);

        // 自分の出品商品は表示されない
        $response->assertDontSee('自分の商品');

        // 他ユーザーの出品商品は表示される
        $response->assertSee('他人の商品');
    }

    /**
     * mylist スコープ：ログイン中、いいねした商品のみが表示されること
     */
    public function test_mylist_scope_shows_only_favorited_items()
    {
        // ログインユーザーと出品者を作成
        $loginUser = User::factory()->create(['name' => 'テストユーザー1']);
        $seller = User::factory()->create(['name' => '出品者']);

        // 商品を2つ作成
        $favoritedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'いいね済み商品',
        ]);
        $notFavoritedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'いいねしていない商品',
        ]);

        // ログインユーザーが1つ目の商品にいいね
        Favorite::create([
            'user_id' => $loginUser->id,
            'item_id' => $favoritedItem->id,
        ]);

        // ログインしてマイリストタブを表示
        $response = $this->actingAs($loginUser)
            ->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);

        // いいねした商品のみ表示される
        $response->assertSee('いいね済み商品');

        // いいねしていない商品は表示されない
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * keyword スコープ：キーワード検索で該当する商品のみが表示されること
     */
    public function test_keyword_scope_filters_items_by_name()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        // 検索に該当する商品
        $matchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '高性能ノートPC',
        ]);

        // 検索に該当しない商品
        $nonMatchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '革製ショルダーバッグ',
        ]);

        // キーワード「ノートPC」で検索
        $response = $this->get(route('items.index', [
            'tab' => 'recommend',
            'keyword' => 'ノートPC',
        ]));

        $response->assertStatus(200);

        // 該当する商品が表示される
        $response->assertSee('高性能ノートPC');

        // 該当しない商品は表示されない
        $response->assertDontSee('革製ショルダーバッグ');
    }

    /**
     * 検索状態の維持：検索時に tab パラメータが正しく引き継がれていること
     */
    public function test_search_preserves_tab_parameter()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        // 商品を作成
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
        ]);

        // おすすめタブ + キーワード検索
        $response = $this->actingAs($user)
            ->get(route('items.index', [
                'tab' => 'recommend',
                'keyword' => 'テスト',
            ]));

        $response->assertStatus(200);

        // タブ切り替えリンクに keyword パラメータが引き継がれていることを確認
        // おすすめタブのリンクに keyword が含まれている
        $response->assertSee('tab=recommend');
        $response->assertSee('tab=mylist');

        // マイリストタブ + キーワード検索
        $response = $this->actingAs($user)
            ->get(route('items.index', [
                'tab' => 'mylist',
                'keyword' => 'テスト',
            ]));

        $response->assertStatus(200);

        // マイリストタブでもキーワードがリンクに含まれている
        $response->assertSee('tab=recommend');
        $response->assertSee('tab=mylist');
    }
}
