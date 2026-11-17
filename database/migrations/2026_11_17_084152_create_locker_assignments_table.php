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
       Schema::create('locker_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('locker_id')->constrained('storage_lockers')->cascadeOnDelete();
        $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
        $table->integer('quantity_assigned');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locker_assignments');
    }
};
