<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'type', 'message', 'severity', 'is_read', 'status'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'activa');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
