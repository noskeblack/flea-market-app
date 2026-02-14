<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. マスターデータ（先に投入）
        $this->call([
            ConditionsTableSeeder::class,
            CategoriesTableSeeder::class,
        ]);

        // 2. ユーザー＆プロフィール
        $this->call([
            UsersTableSeeder::class,
        ]);

        // 3. 商品（ユーザー・マスターデータに依存）
        $this->call([
            ItemsTableSeeder::class,
        ]);
    }
}
