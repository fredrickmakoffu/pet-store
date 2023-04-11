<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'content',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function scopeFilter($query, array $filters)
    {   
        $title = $filters['title'] ?? null;

        $query->when($title, function($query) use ($title) {
            $query->where('title', 'LIKE', "%{$title}%");
        });
    }
}
