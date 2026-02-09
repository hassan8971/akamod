<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AppPage extends Model {
    protected $guarded = [];
    
    public function sections() {
        return $this->hasMany(AppSection::class)->orderBy('sort_order');
    }
}