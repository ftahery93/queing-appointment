<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Area extends Model {

    protected $fillable = ['name_en', 'name_ar', 'governorate_id', 'status', 'created_at', 'updated_at'];
    
    /**
     * Get the governorates
     */
    public function governorates()
    {
        return $this->belongsTo('App\Models\Admin\Governorates', 'governorate_id', 'id');
    }

}
