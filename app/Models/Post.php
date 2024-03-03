<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post extends Model
{
    use HasFactory;

    public function __construct(public $title, public $excerpt, public $date, public $body, public $slug, public $id)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
        $this->id = $id;
    }

    public static function allPostsFiles()
    {
        return cache()->rememberForever('posts.all', function () {
            return collect(File::files(resource_path('posts')))
                ->map(function ($file) {
                    return YamlFrontMatter::parseFile($file);
                })
                ->map(function ($file) {
                    return new Post($file->title, $file->excerpt, $file->date, $file->body(), $file->slug, $file->id);
                })
                ->sortByDesc('id');
        });
    }

    public static function find($slug)
    {
        $posts = static::allPostsFiles();

        return $posts->firstWhere('slug', "$slug");
    }
}
