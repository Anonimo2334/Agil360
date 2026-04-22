<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires redefining the full ENUM to add a value.
        // We add 'progreso_bajo' to the existing list.
        DB::statement("ALTER TABLE `alerts` MODIFY COLUMN `type` ENUM(
            'riesgo',
            'vencido',
            'sin_tareas',
            'sin_actualizacion',
            'tareas_atrasadas',
            'progreso_bajo'
        ) NOT NULL DEFAULT 'riesgo'");
    }

    public function down(): void
    {
        // First delete any rows that use the value being removed, then revert
        DB::table('alerts')->where('type', 'progreso_bajo')->delete();

        DB::statement("ALTER TABLE `alerts` MODIFY COLUMN `type` ENUM(
            'riesgo',
            'vencido',
            'sin_tareas',
            'sin_actualizacion',
            'tareas_atrasadas'
        ) NOT NULL DEFAULT 'riesgo'");
    }
};
