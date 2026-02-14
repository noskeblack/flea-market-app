<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テストログイン用ユーザー（固定）
        $testUser = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);
        Profile::create([
            'user_id' => $testUser->id,
            'zipcode' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => 'テストビル101',
        ]);

        // 追加のダミーユーザー 2名
        User::factory(2)->create()->each(function ($user) {
            Profile::factory()->create(['user_id' => $user->id]);
        });
    }
}
