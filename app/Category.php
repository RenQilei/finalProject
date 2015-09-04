<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name', 'display_name', 'description', 'manager', 'department_id', 'parent_category'];

    public function department() {
        return $this->hasOne('App\Department');
    }

    public function templates() {
        return $this->hasMany('App\Template');
    }
}
