<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateSection extends Model
{
    protected $table = 'template_sections';

    protected $fillable = ['name', 'display_name', 'description', 'content', 'is_editable', 'template_id'];

    public function template() {
        return $this->hasOne('App\Template');
    }
}
