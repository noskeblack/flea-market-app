<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * この状態に該当する商品（1対多）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
