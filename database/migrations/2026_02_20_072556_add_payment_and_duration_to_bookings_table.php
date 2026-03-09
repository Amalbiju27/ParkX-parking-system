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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_category_id')->nullable()->after('slot_id');
            $table->integer('duration_hours')->default(1)->after('vehicle_category_id');
            $table->decimal('amount', 8, 2)->default(0)->after('duration_hours');
            $table->string('payment_status')->default('pending')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['vehicle_category_id', 'duration_hours', 'amount', 'payment_status']);
        });
    }
};
