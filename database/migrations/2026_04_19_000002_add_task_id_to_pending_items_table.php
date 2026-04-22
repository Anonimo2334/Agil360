<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_items', 'task_id')) {
                $table->foreignId('task_id')->nullable()->after('project_id')->constrained('tasks')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_items', function (Blueprint $table) {
            if (Schema::hasColumn('pending_items', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            }
        });
    }
};
