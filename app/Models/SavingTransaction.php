<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'saving_plan_id',
        'amount',
        'type',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function savingPlan()
    {
        return $this->belongsTo(SavingPlan::class);
    }
}
