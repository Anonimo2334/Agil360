<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bonus::with(['engineer', 'project.company', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('engineer_id')) {
            $query->where('engineer_id', $request->engineer_id);
        }

        $bonuses   = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero']))->where('is_active', true)->get();

        // Stats summary
        $totalMes = Bonus::whereMonth('created_at', now()->month)->sum('amount');
        $bonosGenerados = Bonus::count();
        $bonosPendientes = Bonus::where('status', 'pendiente')->sum('amount');
        $bonosPagados = Bonus::where('status', 'pagado')->sum('amount');

        // Ranking (only approved or pagado, grouped by engineer)
        $ranking = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero']))
            ->withCount(['bonuses as bonos_count' => fn($q) => $q->whereIn('status', ['aprobado', 'pagado'])])
            ->withSum(['bonuses as bonos_total' => fn($q) => $q->whereIn('status', ['aprobado', 'pagado'])], 'amount')
            ->orderByDesc('bonos_total')
            ->take(5)
            ->get();

        return view('pages.agil365.bonos.index', compact('bonuses', 'engineers', 'totalMes', 'bonosGenerados', 'bonosPendientes', 'bonosPagados', 'ranking'), ['title' => 'Bonos por Cumplimiento']);
    }

    public function approve(Bonus $bonus)
    {
        $bonus->update([
            'status'      => 'aprobado',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Bono aprobado.');
    }

    public function markPaid(Bonus $bonus)
    {
        $bonus->update(['status' => 'pagado']);
        return redirect()->back()->with('success', 'Bono marcado como pagado.');
    }

    public function reject(Bonus $bonus)
    {
        $bonus->update(['status' => 'rechazado']);
        return redirect()->back()->with('success', 'Bono rechazado.');
    }
}
