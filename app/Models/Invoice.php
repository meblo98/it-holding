<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'type',
        'parent_invoice_id',
        'quote_id',
        'user_id',
        'client_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'due_date',
        'notes',
        'share_token',
        'payment_method',
    ];

    protected $casts = [
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'client_id' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function parentInvoice()
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }
}
