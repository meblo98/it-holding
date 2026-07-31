<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'amount',
        'category',
        'payment_method',
        'bank_account_id',
        'bank_transaction_id',
        'expense_date',
        'attachment',
        'description',
        'user_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Categories definition
    const CATEGORIES = [
        'supplies'  => 'Fournitures de bureau',
        'rent'      => 'Loyer / Immobilier',
        'salaries'  => 'Salaires & Rémunérations',
        'travel'    => 'Déplacements & Transports',
        'marketing' => 'Marketing & Publicité',
        'telecom'   => 'Télécoms & Internet',
        'utilities' => 'Électricité & Eau',
        'other'     => 'Autre dépense',
    ];

    // Payment methods definition
    const PAYMENT_METHODS = [
        'cash'          => 'Espèces / Caisse',
        'bank_transfer' => 'Virement bancaire',
        'check'         => 'Chèque',
        'card'          => 'Carte bancaire',
        'other'         => 'Autre moyen',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bankTransaction()
    {
        return $this->belongsTo(BankTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getPaymentMethodLabelAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }
}
