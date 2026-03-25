<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'department',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'last_login_at'     => 'datetime',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function primaryProjects()
    {
        return $this->hasMany(Project::class, 'primary_engineer_id');
    }

    public function backupProjects()
    {
        return $this->hasMany(Project::class, 'backup_engineer_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_engineer_id');
    }

    public function projectNotes()
    {
        return $this->hasMany(ProjectNote::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'engineer_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->role && in_array($this->role->slug, $roleSlugs);
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->role || empty($this->role->permissions)) {
            return false;
        }

        return in_array($permission, $this->role->permissions);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
}
