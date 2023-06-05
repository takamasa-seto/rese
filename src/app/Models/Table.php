<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'seat_num'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
