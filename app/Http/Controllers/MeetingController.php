<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingLog;
use App\Models\Project;
use App\Models\User;
use App\Models\GoogleCalendarIntegration;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /* ─── Helpers ────────────────────────────────────────────────────────── */

    private function log(Meeting $meeting, string $action, ?string $field = null, $old = null, $new = null, ?string $reason = null): void
    {
        MeetingLog::create([
            'meeting_id'    => $meeting->id,
            'user_id'       => auth()->id(),
            'action'        => $action,
            'field_changed' => $field,
            'old_value'     => is_array($old) ? implode(', ', $old) : $old,
            'new_value'     => is_array($new) ? implode(', ', $new) : $new,
            'reason'        => $reason,
        ]);
    }

    private function labelFor(string $field): string
    {
        return match ($field) {
            'title'        => 'Título',
            'meeting_date' => 'Fecha',
            'meeting_time' => 'Hora',
            'status'       => 'Estado',
            'description'  => 'Descripción',
            'location'     => 'Ubicación',
            'project_id'   => 'Proyecto',
            'participants' => 'Participantes',
            default        => $field,
        };
    }

    /* ─── Index ──────────────────────────────────────────────────────────── */

    public function index(Request $request)
    {
        $query = Meeting::with(['project.company', 'participants', 'creator']);

        if ($request->filled('month')) {
            $query->whereMonth('meeting_date', $request->month)
                  ->whereYear('meeting_date', $request->year ?? date('Y'));
        }

        $meetings    = $query->orderBy('meeting_date')->orderBy('meeting_time')->paginate(20)->withQueryString();
        $allMeetings = Meeting::with(['project', 'participants', 'creator'])->orderBy('meeting_date', 'desc')->get();
        $allTasks    = \App\Models\Task::with(['project', 'assignedEngineer'])->get();
        $projects    = Project::whereNotIn('status', ['completado', 'cancelado'])->orderBy('project_name')->get();
        $engineers   = User::where('is_active', true)->orderBy('name')->get();

        // Audit logs para el historial de cambios
        $meetingLogs = MeetingLog::with(['user', 'meeting'])
            ->latest()
            ->take(200)
            ->get();

        return view('pages.agil365.reuniones.index',
            compact('meetings', 'allMeetings', 'allTasks', 'projects', 'engineers', 'meetingLogs'),
            ['title' => 'Reuniones y Calendario']
        );
    }

    /* ─── Store ──────────────────────────────────────────────────────────── */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'project_id'     => 'nullable|exists:projects,id',
            'meeting_date'   => 'required|date',
            'meeting_time'   => 'required',
            'description'    => 'nullable|string',
            'location'       => 'nullable|string|max:255',
            'participants'   => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $participants = $validated['participants'] ?? [];
        unset($validated['participants']);
        $validated['created_by'] = auth()->id();

        $meeting = Meeting::create($validated);
        $meeting->participants()->sync($participants);

        $this->log($meeting, 'creada');

        // Google Calendar Sync
        $integration = GoogleCalendarIntegration::where('user_id', auth()->id())->first();
        if ($integration && $integration->access_token) {
            $googleService = new GoogleCalendarService($integration);
            $googleEventId = $googleService->createMeetingEvent($meeting, User::whereIn('id', $participants)->get()->all());
            if ($googleEventId) {
                $meeting->update(['google_event_id' => $googleEventId]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'meeting' => $meeting->load('project', 'participants', 'creator')]);
        }
        return redirect()->back()->with('success', 'Reunión agendada exitosamente.');
    }

    /* ─── Update ─────────────────────────────────────────────────────────── */

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'project_id'     => 'nullable|exists:projects,id',
            'meeting_date'   => 'required|date',
            'meeting_time'   => 'required',
            'description'    => 'nullable|string',
            'location'       => 'nullable|string|max:255',
            'status'         => 'in:programada,completada,cancelada',
            'participants'   => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'reason'         => 'nullable|string|max:500',
        ]);

        $reason       = $validated['reason'] ?? null;
        $participants = $validated['participants'] ?? [];
        unset($validated['participants'], $validated['reason']);

        // Detect changed scalar fields
        $trackFields = ['title', 'meeting_date', 'meeting_time', 'description', 'location', 'project_id', 'status'];
        foreach ($trackFields as $field) {
            $oldVal = $meeting->getRawOriginal($field);
            $newVal = $validated[$field] ?? null;
            if ((string) $oldVal !== (string) $newVal) {
                $action = $field === 'status' ? 'estado_cambiado' : 'editada';
                $this->log($meeting, $action, $field, $oldVal, $newVal, $reason);
            }
        }

        // Detect participant changes
        $oldParts = $meeting->participants->pluck('name')->sort()->values()->toArray();
        $meeting->update($validated);
        $meeting->participants()->sync($participants);
        $meeting->load('participants');
        $newParts = $meeting->participants->pluck('name')->sort()->values()->toArray();
        if ($oldParts !== $newParts) {
            $this->log($meeting, 'editada', 'participants', implode(', ', $oldParts) ?: '—', implode(', ', $newParts) ?: '—', $reason);
        }

        // Google Calendar Sync
        if ($meeting->google_event_id) {
            $integration = GoogleCalendarIntegration::where('user_id', $meeting->created_by)->first();
            if ($integration && $integration->access_token) {
                $googleService = new GoogleCalendarService($integration);
                // If it's cancelled, maybe we just delete it from Google Calendar, or update the title to [CANCELADA]
                if ($meeting->status === 'cancelada') {
                    $meeting->title = '[CANCELADA] ' . $meeting->title;
                }
                $googleService->updateMeetingEvent($meeting->google_event_id, $meeting);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'meeting' => $meeting->load('project', 'participants', 'creator')]);
        }
        return redirect()->back()->with('success', 'Reunión actualizada.');
    }

    /* ─── Update date (drag & drop) ─────────────────────────────────────── */

    public function updateDate(Request $request, Meeting $meeting)
    {
        $request->validate(['new_date' => 'required|date']);
        $old = $meeting->getRawOriginal('meeting_date');
        $meeting->update(['meeting_date' => $request->new_date]);
        $this->log($meeting, 'fecha_cambiada', 'meeting_date', $old, $request->new_date);
        
        // Google Calendar Sync
        if ($meeting->google_event_id) {
            $integration = GoogleCalendarIntegration::where('user_id', $meeting->created_by)->first();
            if ($integration && $integration->access_token) {
                $googleService = new GoogleCalendarService($integration);
                $googleService->updateMeetingEvent($meeting->google_event_id, $meeting);
            }
        }
        
        return response()->json(['success' => true]);
    }

    /* ─── Update status (AJAX quick action) ─────────────────────────────── */

    public function updateStatus(Request $request, Meeting $meeting)
    {
        $request->validate(['status' => 'required|in:programada,completada,cancelada']);
        $old = $meeting->status;
        $meeting->update(['status' => $request->status]);
        $this->log($meeting, 'estado_cambiado', 'status', $old, $request->status, $request->reason);
        
        // Google Calendar Sync
        if ($meeting->google_event_id) {
            $integration = GoogleCalendarIntegration::where('user_id', $meeting->created_by)->first();
            if ($integration && $integration->access_token) {
                $googleService = new GoogleCalendarService($integration);
                if ($meeting->status === 'cancelada') {
                    $meeting->title = '[CANCELADA] ' . $meeting->title;
                }
                $googleService->updateMeetingEvent($meeting->google_event_id, $meeting);
            }
        }
        
        return response()->json(['success' => true, 'status' => $request->status]);
    }

    /* ─── Destroy ────────────────────────────────────────────────────────── */

    public function destroy(Meeting $meeting)
    {
        // Log before deleting
        MeetingLog::create([
            'meeting_id'    => $meeting->id,
            'user_id'       => auth()->id(),
            'action'        => 'eliminada',
            'field_changed' => null,
            'old_value'     => $meeting->title,
            'new_value'     => null,
            'reason'        => null,
        ]);

        // Google Calendar Sync
        if ($meeting->google_event_id) {
            $integration = GoogleCalendarIntegration::where('user_id', $meeting->created_by)->first();
            if ($integration && $integration->access_token) {
                $googleService = new GoogleCalendarService($integration);
                $googleService->deleteMeetingEvent($meeting->google_event_id);
            }
        }

        $meeting->delete();
        return redirect()->back()->with('success', 'Reunión eliminada.');
    }

    /* ─── Show detail (AJAX) ─────────────────────────────────────────────── */

    public function show(Meeting $meeting)
    {
        $meeting->load('project', 'participants', 'creator', 'logs.user');
        return response()->json($meeting);
    }

    /* ─── Meeting logs (AJAX) ────────────────────────────────────────────── */

    public function logs(Request $request)
    {
        $query = MeetingLog::with(['user', 'meeting'])
            ->latest();

        if ($request->filled('meeting_id')) {
            $query->where('meeting_id', $request->meeting_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return response()->json($query->take(300)->get());
    }
}
