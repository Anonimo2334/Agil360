<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['programada', 'completada', 'cancelada'])->default('programada');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meetings');
    }
};
