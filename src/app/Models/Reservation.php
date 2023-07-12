<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'number_of_people'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tables(): BelongsToMany
    {
        return $this->belongsToMany(Table::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
    
    public function scopeEndsAfterSearch($query, $time)
    {
        if (!empty($time)) {
            $query->where('end_time', '>', $time);
        }
    }

    public function scopeStartsBeforeSearch($query, $time)
    {
        if (!empty($time)) {
            $query->where('start_time', '<', $time);
        }
    }

    public function scopeUserIdSearch($query, $user_id)
    {
        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }
    }
}
