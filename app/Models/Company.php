<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'whatsapp',
        'email',
        'website',
        'country',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function activeProjects()
    {
        return $this->hasMany(Project::class)->where('status', '!=', 'completado');
    }
}
