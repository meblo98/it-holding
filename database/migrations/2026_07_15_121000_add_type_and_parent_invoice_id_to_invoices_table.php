<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('type')->default('invoice')->after('number'); // 'invoice', 'proforma', 'credit_note'
            $table->foreignId('parent_invoice_id')->nullable()->after('type')->constrained('invoices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['parent_invoice_id']);
            $table->dropColumn(['type', 'parent_invoice_id']);
        });
    }
};
