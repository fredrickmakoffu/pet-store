<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
    ];

    public function scopeFilter($query, array $filters)
    {
        $title = $filters['title'] ?? null;
        $slug = $filters['slug'] ?? null;

        $query->when($title, function($query) use ($title) {
            $query->where('title', 'LIKE', "%{$title}%");
        })
        ->when($slug, function($query) use ($slug) {
            $query->where('slug', 'LIKE', "%{$slug}%");
        });
    }
}
