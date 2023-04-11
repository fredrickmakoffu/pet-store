<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'price',
        'category_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $title = $filters['title'] ?? null;
        $slug = $filters['price'] ?? null;
        $min_price = $filters['min_price'] ?? null;
        $max_price = $filters['max_price'] ?? null;
        $category_id = $filters['category_id'] ?? null;

        $query->when($title, function($query) use ($title) {
            $query->where('title', 'LIKE', "%{$title}%");
        })
        ->when($min_price && $max_price, function($query) use ($min_price, $max_price) {
            $query->whereBetween('price', [$min_price, $max_price]);
        })
        ->when($slug, function($query) use ($category_id) {
            $query->where('category_id', 'LIKE', "%{$category_id}%");
        });
    }
}
