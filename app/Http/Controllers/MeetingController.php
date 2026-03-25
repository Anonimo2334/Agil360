<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::with(['project.company', 'participants', 'creator']);

        if ($request->filled('month')) {
            $query->whereMonth('meeting_date', $request->month)
                  ->whereYear('meeting_date', $request->year ?? date('Y'));
        }

        $meetings    = $query->orderBy('meeting_date')->orderBy('meeting_time')->paginate(20)->withQueryString();
        $allMeetings = Meeting::with(['project', 'participants'])->orderBy('meeting_date', 'desc')->get();
        $allTasks    = \App\Models\Task::with(['project', 'assignedEngineer'])->get();
        $projects    = Project::whereNotIn('status', ['completado', 'cancelado'])->orderBy('project_name')->get();
        $engineers   = User::where('is_active', true)->orderBy('name')->get();

        return view('pages.agil365.reuniones.index',
            compact('meetings', 'allMeetings', 'allTasks', 'projects', 'engineers'),
            ['title' => 'Reuniones y Calendario']
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'project_id'   => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $participants = $validated['participants'] ?? [];
        unset($validated['participants']);
        $validated['created_by'] = auth()->id();

        $meeting = Meeting::create($validated);
        $meeting->participants()->sync($participants);

        return redirect()->back()->with('success', 'Reunión agendada exitosamente.');
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'project_id'   => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'status'       => 'in:programada,completada,cancelada',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $participants = $validated['participants'] ?? [];
        unset($validated['participants']);

        $meeting->update($validated);
        $meeting->participants()->sync($participants);

        return redirect()->back()->with('success', 'Reunión actualizada.');
    }

    public function updateDate(Request $request, Meeting $meeting)
    {
        $request->validate(['new_date' => 'required|date']);
        $meeting->update(['meeting_date' => $request->new_date]);
        return response()->json(['success' => true]);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->back()->with('success', 'Reunión eliminada.');
    }
}
