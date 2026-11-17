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
       Schema::create('storage_lockers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('warehouse_id')->constrained('warehouse_hubs')->cascadeOnDelete();
        $table->string('locker_code')->unique();
        $table->decimal('capacity', 10, 3);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_lockers');
    }
};
