<?php

namespace Database\Factories;

use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $items = [
            ['name' => '腕時計', 'brand' => 'セイコー', 'description' => 'スタイリッシュなデザインのメンズ腕時計'],
            ['name' => 'HDD', 'brand' => 'バッファロー', 'description' => '高速カクウノウの外付けHDD'],
            ['name' => '玉ねぎ3束', 'brand' => null, 'description' => '新鮮な玉ねぎ3束セット'],
            ['name' => '革靴', 'brand' => 'リーガル', 'description' => 'ビジネスシーンに最適な革靴'],
            ['name' => 'ノートPC', 'brand' => 'レノボ', 'description' => '高性能なノートパソコン'],
            ['name' => 'マイク', 'brand' => 'オーディオテクニカ', 'description' => '高音質のコンデンサーマイク'],
            ['name' => 'ショルダーバッグ', 'brand' => 'コーチ', 'description' => '使いやすいショルダーバッグ'],
            ['name' => 'タンブラー', 'brand' => 'サーモス', 'description' => '保温・保冷機能付きタンブラー'],
            ['name' => 'コーヒーミル', 'brand' => 'カリタ', 'description' => '手動式のコーヒーミル'],
            ['name' => 'メイクセット', 'brand' => null, 'description' => '初心者向けメイクアップセット'],
        ];

        $item = $this->faker->randomElement($items);

        return [
            'user_id' => User::factory(),
            'condition_id' => Condition::inRandomOrder()->first()->id ?? 1,
            'name' => $item['name'],
            'brand' => $item['brand'],
            'price' => $this->faker->numberBetween(500, 50000),
            'description' => $item['description'],
            'image' => 'items/dummy_' . $this->faker->numberBetween(1, 10) . '.jpg',
            'is_sold' => false,
        ];
    }
}
