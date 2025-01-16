<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relation many to many
    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    //one treasurer has one general fund (one-one)
    public function general_fund(){
        return $this->hasOne(GeneralFund::class, 'treasurer_id');
    }

    //one treasurer HasMany fund (one-many)
    public function fund(){
        return $this->hasMany(Fund::class, 'treasurer_id');
    }

    // one donator to many invoice(one-many)
    public function invoice(){
        return $this->hasMany(Invoice::class, 'donator_id');
    }
}
