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
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();

        $table->foreignId('parking_space_id')
              ->constrained('parking_spaces')
              ->onDelete('cascade');

        $table->foreignId('category_id')
              ->constrained('vehicle_categories')
              ->onDelete('cascade');

        $table->foreignId('user_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');

        $table->string('vehicle_number');

        $table->timestamp('entry_time');
        $table->timestamp('exit_time')->nullable();

        $table->integer('duration')->nullable(); 

        // CHANGED: Ensure this is decimal to hold cents (e.g., 300.27)
        $table->decimal('charge', 10, 2)->nullable(); 

        // ADDED: This column was missing but required by your controller
        $table->decimal('penalty', 10, 2)->nullable()->default(0);

        $table->enum('status', ['parked', 'exited'])->default('parked');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::dropIfExists('vehicles');
}

};
