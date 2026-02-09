<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AppSection extends Model {
    protected $guarded = [];
    
    protected $casts = [
        'config' => 'array', // Khodkar tabdil be array mishe
        'is_visible' => 'boolean'
    ];

    public function page() {
        return $this->belongsTo(AppPage::class, 'app_page_id');
    }
}