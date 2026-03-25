<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->enum('type', ['cliente', 'ingeniero']);
            $table->text('description');
            $table->enum('status', ['pendiente', 'completado', 'cancelado'])->default('pendiente');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_items');
    }
};
