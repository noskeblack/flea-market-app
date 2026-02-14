<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * このカテゴリに属する商品（多対多）
     */
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
