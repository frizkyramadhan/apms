<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fleet_equipment_id');
            $table->string('status', 20);
            $table->string('priority', 4)->nullable();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->string('closed_to', 20)->nullable();
            $table->decimal('hm_snapshot', 12, 2)->nullable();
            $table->decimal('hm_corrected', 12, 2)->nullable();
            $table->text('problem')->nullable();
            $table->string('mr_no', 30)->nullable();
            $table->string('pr_no', 30)->nullable();
            $table->string('po_no', 30)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fleet_equipment_id', 'ended_at']);
            $table->index(['status', 'started_at']);
            $table->foreign('fleet_equipment_id')
                ->references('fleet_equipment_id')
                ->on('fleet_equipment_cache');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_events');
    }
};
