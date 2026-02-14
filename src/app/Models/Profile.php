<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zipcode',
        'address',
        'building',
    ];

    /**
     * プロフィールの所有ユーザー（1対1の逆）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
