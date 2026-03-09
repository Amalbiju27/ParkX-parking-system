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
    Schema::create('parking_slots', function (Blueprint $table) {
        $table->id();

        $table->foreignId('parking_space_id')
              ->constrained('parking_spaces')
              ->onDelete('cascade');

        $table->string('slot_number');   // e.g., A1, A2, B1
        $table->string('slot_type')->nullable(); // optional (bike/car)

        $table->enum('status', ['available', 'occupied', 'reserved'])
              ->default('available');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('parking_slots');
}

};
