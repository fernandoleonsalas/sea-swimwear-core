<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'type',
        'bank_name',
        'holder_name',
        'holder_id',
        'phone_number',
        'email',
        'account_number',
        'is_active',
    ];
}
