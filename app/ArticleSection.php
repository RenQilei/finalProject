<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleSection extends Model
{
    protected $table = 'article_sections';

    protected $fillable = ['content', 'template_section_id', 'article_id'];

    public function article() {
        return $this->hasOne('App\Article');
    }
}
