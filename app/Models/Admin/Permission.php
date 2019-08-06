<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    protected $fillable = ['name', 'user_role_id', 'groupname', 'permissions', 'status', 'created_at', 'updated_at'];

}
