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
        $query->when(isset($filters['title']), fn ($query, $title) => 
                $query->where('title', $title))
            ->when(isset($filters['content']), fn ($query, $content) => 
                $query->where('content', $content));
    }
}
