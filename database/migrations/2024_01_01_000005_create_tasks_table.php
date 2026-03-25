<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('status', ['pendiente', 'en_progreso', 'completada', 'bloqueada'])->default('pendiente');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
