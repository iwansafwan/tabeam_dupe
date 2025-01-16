<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralFund extends Model
{
    use HasFactory,SoftDeletes;

    protected $table= 'general_funds';

    protected $fillable = [
        'name',
        'collected_amount', 
        'qr_code'
    ];

    //one g_fund to many invoice(one-many)
    public function invoice(){
        return $this->hasMany(Invoice::class,'general_fund_id');
    }
}
