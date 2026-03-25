<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'engineer_id',
        'project_id',
        'amount',
        'status',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendiente'  => 'Pendiente',
            'aprobado'   => 'Aprobado',
            'pagado'     => 'Pagado',
            'rechazado'  => 'Rechazado',
            default      => ucfirst($this->status),
        };
    }
}
