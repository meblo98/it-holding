<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->string('category'); // e.g. supplies, rent, salaries, travel, marketing, telecom, other
            $table->string('payment_method'); // cash, bank_transfer, check, card, other
            
            // Optional links
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null');
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->onDelete('set null');
            
            $table->date('expense_date');
            $table->string('attachment')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // logged by
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
