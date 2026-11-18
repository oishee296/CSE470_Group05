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
        Schema::create('duty_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_profile_id')->constrained('volunteer_profiles')->onDelete('restrict');
            $table->foreignId('disaster_id')->constrained('disasters')->onDelete('restrict');
            $table->foreignId('zone_id')->constrained('zones')->onDelete('restrict');
            $table->string('assigned_responsibility');
            $table->date('assignment_date');
            $table->string('assignment_status')->default('Assigned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duty_assignments');
    }
};
