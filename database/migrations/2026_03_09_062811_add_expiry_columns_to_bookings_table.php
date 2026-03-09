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
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('bookings', 'scanned_at')) {
                $table->timestamp('scanned_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'extended_minutes')) {
                $table->integer('extended_minutes')->default(0)->after('scanned_at');
            }
            if (!Schema::hasColumn('bookings', 'fine_amount')) {
                $table->decimal('fine_amount', 10, 2)->default(0)->after('extended_minutes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'scanned_at', 'extended_minutes', 'fine_amount']);
        });
    }
};
