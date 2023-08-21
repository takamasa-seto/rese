<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'star',
        'comment',
        'image_url'
    ];

    public function scopeShopSearch($query, $shop_id)
    {
        if (!empty($shop_id)) {
            $query->where('shop_id', $shop_id);
        }
    }
    
}
