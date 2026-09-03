<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_equipment_cache', function (Blueprint $table) {
            $table->unsignedInteger('fleet_equipment_id')->primary();
            $table->string('unit_no', 20);
            $table->string('description', 200)->nullable();
            $table->string('project_code', 10);
            $table->unsignedInteger('fleet_model_id');
            $table->string('model_name', 50)->nullable();
            $table->string('manufacture', 50)->nullable();
            $table->string('plant_group', 50)->nullable();
            $table->string('plant_type', 50)->nullable();
            $table->string('unit_status', 20)->nullable();
            $table->string('serial_number', 80)->nullable();
            $table->string('engine_number', 80)->nullable();
            $table->timestamp('synced_at')->useCurrent();
            $table->index('unit_no');
            $table->index('project_code');
            $table->index('fleet_model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_equipment_cache');
    }
};
