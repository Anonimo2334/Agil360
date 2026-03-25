<?php
/**
 * Script para actualizar los permisos de roles en la base de datos.
 * Ejecutar desde la raíz del proyecto: php update_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;

$roles = [
    'admin' => ['clientes.ver','clientes.crear','clientes.editar','clientes.eliminar','proyectos.ver','proyectos.crear','proyectos.editar','proyectos.eliminar','tareas.ver','tareas.crear','tareas.editar','tareas.eliminar','usuarios.gestionar','roles.gestionar','reportes.ver'],
    'gerente' => ['clientes.ver','proyectos.ver','proyectos.crear','proyectos.editar','tareas.ver','tareas.crear','tareas.editar','reportes.ver'],
    'ingeniero' => ['proyectos.ver','tareas.ver','tareas.crear','tareas.editar'],
    'soporte' => ['clientes.ver','proyectos.ver','tareas.ver','tareas.crear','tareas.editar'],
    'visualizador' => ['clientes.ver','proyectos.ver','tareas.ver','reportes.ver'],
];

foreach ($roles as $slug => $permissions) {
    $role = Role::where('slug', $slug)->first();
    if ($role) {
        $role->permissions = $permissions;
        $role->save();
        echo "✅ Rol '{$slug}' actualizado con " . count($permissions) . " permisos.\n";
    } else {
        echo "⚠️  Rol '{$slug}' no encontrado.\n";
    }
}

echo "\n✅ ¡Permisos actualizados correctamente!\n";
