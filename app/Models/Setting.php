<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Key-value system settings. Read through App\Support\Settings (cached).
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];
}
