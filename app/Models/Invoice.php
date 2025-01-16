<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $tableName = 'invoices';

    protected $fillable = [
        'donator_id',
        'general_fund_id',
        'fund_id',
        'ratio_id',
        'donation_type',
        'notes',
        'amount'
    ];

    // many invoice to one donator(many-one)
    public function donator(){
        return $this->belongsTo(User::class, 'donator_id');
    }

    // many invoice to one g_fund(many-one)
    public function general_fund(){
        return $this->belongsTo(GeneralFund::class, 'general_fund_id');
    }

    // many invoice to one fund(many-one)
    public function fund(){
        return $this->belongsTo(Fund::class, 'fund_id');
    }
    
    //many invoice to one ratio(many-one)
    public function ratio(){
        return $this->belongsTo(Ratio::class, 'ratio_id');
    }
}
