<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreFeatureRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected Condition $condition;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->condition = Condition::create(['name' => '良好']);
        $this->category = Category::create(['name' => 'ファッション']);
    }

    public function test_login_function_works_with_valid_credentials(): void
    {
        $password = 'password123';
        $user = User::factory()->create([
            'name' => 'loginuser',
            'email' => 'login-user@example.com',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_function_logs_user_out(): void
    {
        $user = User::factory()->create([
            'name' => 'logoutuser',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_item_detail_information_can_be_retrieved(): void
    {
        $seller = User::factory()->create(['name' => 'detailseller']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
            'name' => '詳細テスト商品',
            'description' => '詳細テスト用の商品説明です',
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('詳細テスト商品');
        $response->assertSee('詳細テスト用の商品説明です');
    }

    public function test_favorite_function_can_store_and_remove_favorite(): void
    {
        $user = User::factory()->create([
            'name' => 'favoriteuser',
            'email_verified_at' => now(),
        ]);
        $seller = User::factory()->create(['name' => 'favoriteseller']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
        ]);

        $storeResponse = $this->actingAs($user)->post(route('favorites.store', ['item_id' => $item->id]));
        $storeResponse->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $destroyResponse = $this->actingAs($user)->delete(route('favorites.destroy', ['item_id' => $item->id]));
        $destroyResponse->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_comment_submission_function_stores_comment(): void
    {
        $user = User::factory()->create([
            'name' => 'commentuser',
            'email_verified_at' => now(),
        ]);
        $seller = User::factory()->create(['name' => 'commentseller']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
            'content' => 'コメント送信テスト',
        ]);

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'コメント送信テスト',
        ]);
    }

    public function test_payment_method_selection_validation_rejects_invalid_value(): void
    {
        $buyer = User::factory()->create([
            'name' => 'paymentbuyer',
            'email_verified_at' => now(),
        ]);
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => 'テストビル101',
        ]);
        $seller = User::factory()->create(['name' => 'paymentseller']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
        ]);

        $response = $this->actingAs($buyer)->from(route('purchase.show', ['item_id' => $item->id]))
            ->post(route('payment.checkout', ['item_id' => $item->id]), [
                'payment_method' => '9',
                'shipping_address' => '東京都渋谷区神宮前1-1-1',
            ]);

        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));
        $response->assertSessionHasErrors('payment_method');
    }

    public function test_shipping_address_change_function_updates_profile_and_session(): void
    {
        $buyer = User::factory()->create([
            'name' => 'addressbuyer',
            'email_verified_at' => now(),
        ]);
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'zipcode' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => '旧ビル101',
        ]);
        $seller = User::factory()->create(['name' => 'addressseller']);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
        ]);

        $response = $this->actingAs($buyer)->post(route('address.update', ['item_id' => $item->id]), [
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => '新ビル202',
        ]);

        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $buyer->id,
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => '新ビル202',
        ]);
        $this->assertEquals('150-0001', session("shipping_address_{$item->id}.zipcode"));
    }

    public function test_user_information_can_be_retrieved_on_mypage(): void
    {
        $user = User::factory()->create([
            'name' => 'プロフィール表示ユーザー',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('mypage.show'));

        $response->assertStatus(200);
        $response->assertSee('プロフィール表示ユーザー');
    }

    public function test_user_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => '更新前ユーザー',
            'email_verified_at' => now(),
        ]);
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => '旧建物',
        ]);

        $response = $this->actingAs($user)->post(route('mypage.update'), [
            'name' => '更新後ユーザー',
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => '新建物',
        ]);

        $response->assertRedirect(route('mypage.show'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新後ユーザー',
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => '新建物',
        ]);
    }

    public function test_sell_item_information_can_be_registered(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create([
            'name' => 'selluser',
            'email_verified_at' => now(),
        ]);
        $image = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($seller)->post(route('items.store'), [
            'name' => '出品テスト商品',
            'description' => '出品テストの説明文',
            'image' => $image,
            'categories' => [$this->category->id],
            'condition_id' => $this->condition->id,
            'brand' => 'テストブランド',
            'price' => 12000,
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', [
            'user_id' => $seller->id,
            'condition_id' => $this->condition->id,
            'name' => '出品テスト商品',
            'price' => 12000,
        ]);

        $item = Item::where('name', '出品テスト商品')->firstOrFail();
        $this->assertDatabaseHas('category_item', [
            'category_id' => $this->category->id,
            'item_id' => $item->id,
        ]);
    }
}
