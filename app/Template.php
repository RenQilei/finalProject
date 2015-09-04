<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';

    protected $fillable = ['name', 'display_name', 'description', 'category_id'];

    public function category() {
        return $this->hasOne('App\Category');
    }

    public function templateSections() {
        return $this->hasMany('App\TemplateSection');
    }
}
