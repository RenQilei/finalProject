<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = ['title', 'author', 'category_id', 'template_id'];

    public function category() {
        return $this->hasOne('App\Category');
    }

    public function articleSections() {
        return $this->hasMany('App\ArticleSection');
    }
}
