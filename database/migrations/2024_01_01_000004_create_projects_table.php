<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('project_name');
            $table->string('ceo')->nullable();
            $table->foreignId('primary_engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('backup_engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->enum('status', ['iniciado', 'en_proceso', 'soporte', 'completado', 'cancelado'])->default('iniciado');
            $table->string('platform')->nullable();
            $table->string('bot_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('server_hosting')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_at_risk')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
