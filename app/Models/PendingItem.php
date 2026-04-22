<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingItem extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'task_id', 'type', 'description', 'status', 'assigned_to', 'resolution_note'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByClient($query)
    {
        return $query->where('type', 'cliente');
    }

    public function scopeByEngineer($query)
    {
        return $query->where('type', 'ingeniero');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }
}
