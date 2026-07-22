<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'product_id',
        'service_id',
        'target_amount',
        'current_amount',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 2));
    }
}
