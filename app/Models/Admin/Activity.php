<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'status', 'icon', 'created_at', 'updated_at'];
}
