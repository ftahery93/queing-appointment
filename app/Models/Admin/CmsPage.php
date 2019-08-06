<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model {
    
    protected $table = 'cmspages';

  protected $fillable = [
        'name_en', 'name_ar', 'description_en', 'description_ar', 'status', 'created_at', 'updated_at'];

}
