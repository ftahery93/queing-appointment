<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class SubscriberPackageDetail extends Model {
    
   public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
    protected $guarded = [];
}
