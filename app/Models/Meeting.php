<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id',
        'meeting_date',
        'meeting_time',
        'description',
        'location',
        'status',
        'created_by',
        'google_event_id',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'meeting_participants');
    }

    public function logs()
    {
        return $this->hasMany(MeetingLog::class)->with('user')->latest();
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->meeting_time));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('meeting_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('meeting_date', '>=', today())->where('status', 'programada');
    }
}
