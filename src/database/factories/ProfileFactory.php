<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $prefectures = [
            '北海道', '東京都', '大阪府', '愛知県', '福岡県',
            '神奈川県', '埼玉県', '千葉県', '兵庫県', '京都府',
        ];

        return [
            'user_id' => User::factory(),
            'zipcode' => $this->faker->numerify('###-####'),
            'address' => $this->faker->randomElement($prefectures) . $this->faker->city() . $this->faker->streetAddress(),
            'building' => $this->faker->optional(0.5)->buildingNumber() ? 'テストマンション' . $this->faker->buildingNumber() : null,
        ];
    }
}
