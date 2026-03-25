<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engineer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->default(50.00);
            $table->enum('status', ['pendiente', 'aprobado', 'pagado', 'rechazado'])->default('pendiente');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
