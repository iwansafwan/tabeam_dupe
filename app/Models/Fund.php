<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fund extends Model
{
    use HasFactory, SoftDeletes;

    // If you're using a different table name, uncomment and define it:
    protected $table = 'funds';

    // Define fillable fields
    protected $fillable = [
        'treasurer_id',
        'name',
        'target_amount',
        'end_date',
        'description',
        'image',
        'qr_code',
        'status',
    ];

    // many funds belong to one treasurer(many-one)
    public function treasurer(){
        return $this->belongsTo(User::class,'treasurer_id');
    }

    // one fund to many invoice(one-many)
    public function invoice(){
        return $this->hasMany(Invoice::class,'fund_id');
    }

    // one fund has many ratio(one-many)
    public function ratio(){
        return $this ->hasMany(Ratio::class,'fund_id');
    }
}
