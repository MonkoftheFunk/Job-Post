<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $fillable = ['title', 'slug', 'company', 'location', 'logo',
        'is_highlighted', 'is_active', 'link', 'content', 'tag_csg', 'created_at'];
}
