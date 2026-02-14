<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'zipcode',
        'address',
        'building',
    ];

    /**
     * 購入者（多対1）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 購入した商品（多対1）
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
