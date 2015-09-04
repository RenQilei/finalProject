<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = ['name', 'display_name', 'description', 'manager', 'parent_department'];

    public function users() {
        return $this->belongsToMany('App\User');
    }

    public function categories() {
        return $this->hasMany('App\Category');
    }
}
