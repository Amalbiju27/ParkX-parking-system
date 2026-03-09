<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('vehicles', function (Blueprint $table) {
        $table->foreignId('slot_id')
              ->nullable()
              ->constrained('parking_slots')
              ->nullOnDelete();
    });
}



    /**
     * Reverse the migrations.
     */
 public function down()
{
    Schema::table('vehicles', function (Blueprint $table) {
        $table->dropConstrainedForeignId('slot_id');
    });
}
};
