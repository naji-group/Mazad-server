<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
      
'name',
'value',
'category',
'type',
'sequence',
'image',
'create_user_id',
'update_user_id',

    ];
}
