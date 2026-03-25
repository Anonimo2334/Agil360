<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_name',
        'ceo',
        'primary_engineer_id',
        'backup_engineer_id',
        'start_date',
        'end_date',
        'progress_percentage',
        'status',
        'platform',
        'bot_name',
        'website_url',
        'server_hosting',
        'notes',
        'is_at_risk',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_at_risk'  => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function primaryEngineer()
    {
        return $this->belongsTo(User::class, 'primary_engineer_id');
    }

    public function backupEngineer()
    {
        return $this->belongsTo(User::class, 'backup_engineer_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function notes()
    {
        return $this->hasMany(ProjectNote::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function pendingItems()
    {
        return $this->hasMany(PendingItem::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'iniciado'    => 'Iniciado',
            'en_proceso'  => 'En proceso',
            'soporte'     => 'Soporte',
            'completado'  => 'Completado',
            'cancelado'   => 'Cancelado',
            default       => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completado'  => 'success',
            'en_proceso'  => 'blue-light',
            'soporte'     => 'warning',
            'iniciado'    => 'brand',
            'cancelado'   => 'gray',
            default       => 'gray',
        };
    }

    public function getProgressColorAttribute(): string
    {
        if ($this->progress_percentage >= 80) return 'success';
        if ($this->progress_percentage >= 50) return 'blue-light';
        return 'error';
    }

    public function checkIfAtRisk(): bool
    {
        if (!$this->start_date || !$this->end_date) return false;
        if ($this->progress_percentage >= 50) return false;

        $totalDays = $this->start_date->diffInDays($this->end_date, false);
        if ($totalDays <= 0) return false;

        // Si aún no empieza, no está en riesgo
        if (now()->lt($this->start_date)) return false;

        $remaining = now()->diffInDays($this->end_date, false);
        
        // Si ya se venció el tiempo y no llega al 50%, definitivamente está en riesgo
        if ($remaining <= 0) return true;

        $timeRemainingPercentage = ($remaining / $totalDays) * 100;

        return $timeRemainingPercentage < 30;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date) return 0;
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completado';
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['iniciado', 'en_proceso']);
    }

    public function scopeAtRisk($query)
    {
        return $query->where('is_at_risk', true);
    }

    public function scopeForEngineer($query, $engineerId)
    {
        return $query->where('primary_engineer_id', $engineerId)
                     ->orWhere('backup_engineer_id', $engineerId);
    }
}
