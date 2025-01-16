<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ratio extends Model
{
    use HasFactory,SoftDeletes;

    protected $table= 'ratios';
    protected $fillable= [
        'fund_id',
        'category_name',
        'percentage',
        'percent_amount',
        'total_collected',
    ];

    //many ratio to one fund(many to one)
    public function fund(){
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    // one ratio has many invoice(one-many)
    public function invoice(){
        return $this->hasMany(Invoice::class, 'ratio_id');
    }
}
