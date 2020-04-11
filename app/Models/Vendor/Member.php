<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member  extends Model {

    use SoftDeletes;
    
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'mobile', 'package_id', 'package_name','price','cash', 'knet', 'gender_id', 'start_date', 'end_date', 'created_at', 'updated_at', 'dob', 'area_id', 'status', 'dob', 'subscribed_from', 'subscriber_id'
    ,'subscription'];
    
    protected $dates = ['deleted_at'];
     
     
}
