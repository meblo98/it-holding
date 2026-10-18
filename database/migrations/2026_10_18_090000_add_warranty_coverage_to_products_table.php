<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'warranty_coverage_percent')) {
                // 25, 50 ou 75 : part du prix de réparation prise en charge. NULL = aucune.
                $table->unsignedTinyInteger('warranty_coverage_percent')->nullable()->after('warranty_duration_months');
            }
            if (!Schema::hasColumn('products', 'warranty_coverage_scope')) {
                // Ce qui est couvert, ex. « Écran cassé ».
                $table->string('warranty_coverage_scope', 120)->nullable()->after('warranty_coverage_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['warranty_coverage_percent', 'warranty_coverage_scope']);
        });
    }
};
