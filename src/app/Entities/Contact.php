<?php

namespace VCComponent\Laravel\Contact\Entities;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'email',
        'full_name',
        'first_name',
        'last_name',
        'address',
        'phone_number',
        'note',
        'type',
        'status',
    ];

    public function ableToUse($user)
    {
        return true;
    }

}
