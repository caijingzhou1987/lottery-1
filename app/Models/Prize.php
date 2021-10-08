<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'title', 'url','probability', 'status'
    ];

    protected $casts = [
        'status' => 'boolean', // status 是一个布尔类型的字段
    ];
    
    public $timestamps = false;
}
