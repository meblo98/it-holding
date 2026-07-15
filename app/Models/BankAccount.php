<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bank_name',
        'iban',
        'rib',
        'initial_balance',
        'current_balance',
    ];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }
}
