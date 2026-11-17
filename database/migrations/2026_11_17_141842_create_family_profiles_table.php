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
        Schema::create('family_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); // one profile per citizen
            $table->string('area');                              // region used for regional weights
            $table->unsignedSmallInteger('household_size');
            $table->unsignedSmallInteger('infants_count')->default(0);
            $table->unsignedSmallInteger('elderly_count')->default(0);
            $table->unsignedSmallInteger('injured_count')->default(0);
            $table->unsignedInteger('severity_weight')->default(0); // computed in model
            $table->string('severity_level')->default('low');       // low|medium|high|critical
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_profiles');
    }
};
