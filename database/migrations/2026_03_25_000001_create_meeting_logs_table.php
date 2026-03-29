<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['creada', 'editada', 'eliminada', 'estado_cambiado', 'fecha_cambiada']);
            $table->string('field_changed')->nullable();   // campo modificado
            $table->text('old_value')->nullable();         // valor anterior
            $table->text('new_value')->nullable();         // valor nuevo
            $table->text('reason')->nullable();            // motivo del cambio (opcional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_logs');
    }
};
