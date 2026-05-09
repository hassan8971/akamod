<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = ['method_key', 'title', 'cost', 'description', 'is_active'];
}
