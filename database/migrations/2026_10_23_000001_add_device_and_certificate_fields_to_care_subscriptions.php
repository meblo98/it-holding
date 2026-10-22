<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_subscriptions', function (Blueprint $table) {
            $table->string('public_token', 64)->nullable()->unique();
            $table->string('device_category', 30)->nullable();
            $table->string('device_brand')->nullable();
            $table->string('device_model')->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('initial_condition')->nullable();
            $table->json('device_photos')->nullable();
            $table->string('invoice_path')->nullable();
            // none | pending | approved | rejected
            $table->string('device_status', 20)->default('none');
            $table->text('device_rejection_reason')->nullable();
            $table->timestamp('device_submitted_at')->nullable();
            $table->timestamp('device_reviewed_at')->nullable();
            $table->timestamp('certificate_issued_at')->nullable();
        });

        // Abonnements créés auparavant par l'équipe avec un appareil déjà connu : considérés comme validés.
        DB::table('care_subscriptions')
            ->where('product_name', '!=', 'À enregistrer')
            ->update(['device_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('care_subscriptions', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn([
                'public_token', 'device_category', 'device_brand', 'device_model', 'purchase_date',
                'initial_condition', 'device_photos', 'invoice_path', 'device_status',
                'device_rejection_reason', 'device_submitted_at', 'device_reviewed_at', 'certificate_issued_at',
            ]);
        });
    }
};
