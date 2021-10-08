<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class LotteryCode extends Model
{
    use DefaultDatetimeFormat;

    protected $fillable = [
        'code', 'batch_num','prizes_name', 'valid_period', 'prizes_time', 
        'operator', 'award_status'
    ];
    
    protected $casts = [
        'award_status' => 'boolean', // award_status 是一个布尔类型的字段
    ];

    protected $dates = ['valid_period', 'prizes_time'];
}
