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
        Schema::create('relief_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_id')->constrained('disasters')->onDelete('restrict');
            $table->foreignId('zone_id')->constrained('zones')->onDelete('restrict');
            $table->string('resource_name')->nullable();
            $table->unsignedInteger('quantity_requested')->nullable();
            $table->string('request_type')->nullable();
            $table->text('description')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('last_escalated_at')->nullable();
            $table->unsignedTinyInteger('escalation_level')->default(0);
            $table->string('previous_priority')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relief_requests');
    }
};
