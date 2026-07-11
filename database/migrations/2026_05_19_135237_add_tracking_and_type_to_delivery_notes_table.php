<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // Make supplier fields nullable since client delivery notes (envoi) won't have suppliers
            $table->string('supplier_name')->nullable()->change();
            
            // Add tracking and type fields
            $table->string('type')->default('reception'); // 'reception' (supplier), 'envoi' (client)
            $table->string('status')->default('received'); // 'draft', 'pending', 'shipped', 'delivered', 'received'
            
            // Customer tracking fields
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            
            // Relationships to Orders and Invoices
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->string('supplier_name')->nullable(false)->change();
            
            $table->dropForeign(['order_id']);
            $table->dropForeign(['invoice_id']);
            
            $table->dropColumn([
                'type',
                'status',
                'customer_name',
                'customer_phone',
                'customer_address',
                'order_id',
                'invoice_id',
            ]);
        });
    }
};
