<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $query = Alert::with('project.company');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $alerts = $query->where('status', 'activa')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('pages.agil365.alertas.index', compact('alerts'), ['title' => 'Alertas del Sistema']);
    }

    public function markRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Alerta marcada como leída.');
    }

    public function resolve(Alert $alert)
    {
        $alert->update(['status' => 'resuelta']);
        return redirect()->back()->with('success', 'Alerta resuelta.');
    }

    public function ignore(Alert $alert)
    {
        $alert->update(['status' => 'ignorada']);
        return redirect()->back()->with('success', 'Alerta ignorada.');
    }
}
