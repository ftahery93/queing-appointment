<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ImportedFiles extends Model
{
    protected $fillable = ['imported_table_id', 'imported_file', 'created_at', 'updated_at'];
}