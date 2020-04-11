<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model {
    
    protected $table = 'backup_lists';

  protected $fillable = [
        'file_path', 'file_name', 'file_size', 'created_at', 'updated_at'];
  
  
}
