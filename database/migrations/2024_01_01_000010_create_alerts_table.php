<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->enum('type', ['riesgo', 'vencido', 'sin_tareas', 'sin_actualizacion', 'tareas_atrasadas'])->default('riesgo');
            $table->string('message');
            $table->enum('severity', ['warning', 'error'])->default('error');
            $table->boolean('is_read')->default(false);
            $table->enum('status', ['activa', 'resuelta', 'ignorada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
