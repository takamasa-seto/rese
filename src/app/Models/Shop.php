<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region',
        'genre',
        'description',
        'image_url',
        'operation_pattern',
        'time_per_reservation'
    ];

    public function scopeNameSearch($query, $name)
    {
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
    }

    public function scopeRegionSearch($query, $region)
    {
        if (!empty($region)) {
            $query->where('region', $region);
        }
    }

    public function scopeGenreSearch($query, $genre)
    {
        if (!empty($genre)) {
            $query->where('genre', $genre);
        }
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class);
    }

}
