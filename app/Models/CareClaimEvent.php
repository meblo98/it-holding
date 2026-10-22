<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareClaimEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['care_claim_id', 'status', 'note', 'user_id', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function claim() { return $this->belongsTo(CareClaim::class, 'care_claim_id'); }
    public function user()  { return $this->belongsTo(User::class); }
}
