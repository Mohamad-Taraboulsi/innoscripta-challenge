<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;


    protected $table = 'article';

    protected $fillable = [
        'api_source',
        'title',
        'type',
        'author',
        'source_name',
        'url',
        'published_at',
        'content'
    ];
}
