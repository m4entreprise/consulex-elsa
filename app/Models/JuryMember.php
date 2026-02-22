<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JuryMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'detail',
        'description',
        'photo_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        if (str_starts_with($this->photo_path, '/')) {
            return $this->photo_path;
        }

        return '/storage/'.ltrim($this->photo_path, '/');
    }
}
