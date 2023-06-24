<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class);
    }
    
    public function scopeShopIdSearch($query, $shop_id)
    {
        if (!empty($shop_id)) {
            $query->where('shop_id', $shop_id);
        }
    }

    public function scopeSeatNumLargerOrEqualSearch($query, $seat_num)
    {
        if (!empty($seat_num)) {
            $query->where('seat_num', '>=', $seat_num);
        }
    }

}
