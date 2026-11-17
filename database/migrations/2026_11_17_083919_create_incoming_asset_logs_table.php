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
         Schema::create('incoming_asset_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
        $table->integer('quantity_received');
        $table->string('source');
        $table->dateTime('received_at');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_asset_logs');
    }
};
