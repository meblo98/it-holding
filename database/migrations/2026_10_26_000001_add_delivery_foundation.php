<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('service_radius_km')->default(30);
            $table->decimal('rating', 3, 2)->default(3.00); // fiabilité 0 à 5
            $table->boolean('is_active')->default(true);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_lat', 10, 7)->nullable();
            $table->decimal('delivery_lng', 10, 7)->nullable();
            $table->string('delivery_source', 20)->nullable(); // gps | city | admin
        });

        Schema::create('courier_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('radius_km')->default(15);
            $table->boolean('is_available')->default(false);
            $table->timestamp('location_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_profiles');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_lat', 'delivery_lng', 'delivery_source']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'service_radius_km', 'rating', 'is_active']);
        });
    }
};
