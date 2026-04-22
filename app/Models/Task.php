<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'documentation',
        'assigned_engineer_id',
        'priority',
        'status',
        'start_date',
        'due_date',
        'progress',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_engineer_id');
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'baja'    => 'Baja',
            'media'   => 'Media',
            'alta'    => 'Alta',
            'critica' => 'Crítica',
            default   => ucfirst($this->priority),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendiente'    => 'Pendiente',
            'en_progreso'  => 'En progreso',
            'completada'   => 'Completada',
            'bloqueada'    => 'Bloqueada',
            default        => ucfirst($this->status),
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'critica' => 'error',
            'alta'    => 'warning',
            'media'   => 'blue-light',
            'baja'    => 'success',
            default   => 'gray',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completada';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_progreso');
    }
}
