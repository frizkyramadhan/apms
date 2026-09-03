<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_model_cache', function (Blueprint $table) {
            $table->unsignedInteger('fleet_model_id')->primary();
            $table->string('model_name', 50)->nullable();
            $table->string('manufacture', 50)->nullable();
            $table->string('plant_group', 50)->nullable();
            $table->timestamp('synced_at')->useCurrent();
            $table->index('model_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_model_cache');
    }
};
