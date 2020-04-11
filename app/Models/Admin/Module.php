<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'slug', 'icon' ,'description_en', 'description_ar', 'status', 'created_at', 'updated_at'];
}
