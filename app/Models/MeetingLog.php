<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingLog extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
        'action',
        'field_changed',
        'old_value',
        'new_value',
        'reason',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label legible para las acciones
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'creada'          => 'Reunión creada',
            'editada'         => 'Campo editado',
            'eliminada'       => 'Reunión eliminada',
            'estado_cambiado' => 'Estado cambiado',
            'fecha_cambiada'  => 'Fecha cambiada',
            default           => ucfirst($this->action),
        };
    }

    /**
     * Genera un texto descriptivo tipo "Juan cambió el título de X a Y"
     */
    public function getHumanDescriptionAttribute(): string
    {
        $user = $this->user?->name ?? 'Sistema';

        return match ($this->action) {
            'creada'    => "**{$user}** creó la reunión.",
            'eliminada' => "**{$user}** eliminó la reunión.",
            'editada', 'estado_cambiado', 'fecha_cambiada' => (function () use ($user) {
                $field = $this->fieldLabel();
                $old   = $this->old_value ?? '—';
                $new   = $this->new_value ?? '—';
                return "**{$user}** cambió **{$field}** de *{$old}* a *{$new}*.";
            })(),
            default => "**{$user}** realizó una acción: {$this->action}.",
        };
    }

    private function fieldLabel(): string
    {
        return match ($this->field_changed) {
            'title'        => 'el título',
            'meeting_date' => 'la fecha',
            'meeting_time' => 'la hora',
            'status'       => 'el estado',
            'description'  => 'la descripción',
            'location'     => 'la ubicación',
            'project_id'   => 'el proyecto',
            'participants' => 'los participantes',
            default        => $this->field_changed ?? 'un campo',
        };
    }

    /** Color del indicador visual según acción */
    public function getColorAttribute(): string
    {
        return match ($this->action) {
            'creada'          => 'emerald',
            'editada'         => 'blue',
            'estado_cambiado' => 'amber',
            'fecha_cambiada'  => 'purple',
            'eliminada'       => 'red',
            default           => 'gray',
        };
    }
}
