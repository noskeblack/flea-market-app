<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'brand',
        'price',
        'description',
        'image',
        'is_sold',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
    ];

    /**
     * 出品者（多対1）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品の状態（多対1）
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * カテゴリ（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * コメント（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * いいね（1対多）
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * 購入履歴（1対多）
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * 画像URLアクセサ
     * 外部URL（http始まり）はそのまま返し、ローカルパスは storage URL に変換
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
