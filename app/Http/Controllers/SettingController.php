<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $tab = $request->get('tab', 'general');

        $settings = [
            // ── General ────────────────────────────────────────────────────────
            'app_name'                      => Setting::get('app_name', 'Agil365'),
            'notification_email'            => Setting::get('notification_email', 'notificaciones@agil365.com'),
            'timezone'                      => Setting::get('timezone', 'America/Mexico_City'),
            'language'                      => Setting::get('language', 'es'),
            'date_format'                   => Setting::get('date_format', 'd/m/Y'),
            'items_per_page'                => Setting::get('items_per_page', 20),
            'maintenance_mode'              => Setting::get('maintenance_mode', false),

            // ── Alertas ────────────────────────────────────────────────────────
            'alert_risk_enabled'            => Setting::get('alert_risk_enabled', true),
            'alert_risk_threshold_progress' => Setting::get('alert_risk_threshold_progress', 50),
            'alert_risk_threshold_time'     => Setting::get('alert_risk_threshold_time', 30),
            'alert_no_update_days'          => Setting::get('alert_no_update_days', 3),
            'alert_email_notifications'     => Setting::get('alert_email_notifications', true),
            'alert_overdue_projects'        => Setting::get('alert_overdue_projects', true),
            'alert_overdue_tasks'           => Setting::get('alert_overdue_tasks', true),
            'alert_digest_frequency'        => Setting::get('alert_digest_frequency', 'daily'),
            'alert_critical_only_email'     => Setting::get('alert_critical_only_email', false),
            'alert_auto_resolve_days'       => Setting::get('alert_auto_resolve_days', 0),

            // ── Bonos ──────────────────────────────────────────────────────────
            'bonus_enabled'                 => Setting::get('bonus_enabled', true),
            'bonus_amount'                  => Setting::get('bonus_amount', 50),
            'bonus_approval_type'           => Setting::get('bonus_approval_type', 'auto'),
            'bonus_max_per_month'           => Setting::get('bonus_max_per_month', 5),
            'bonus_require_on_time'         => Setting::get('bonus_require_on_time', true),
            'bonus_min_project_progress'    => Setting::get('bonus_min_project_progress', 100),
            'bonus_expiry_days'             => Setting::get('bonus_expiry_days', 30),
            'bonus_currency'                => Setting::get('bonus_currency', 'USD'),
            'bonus_notify_engineer'         => Setting::get('bonus_notify_engineer', true),
            'bonus_notify_admin'            => Setting::get('bonus_notify_admin', true),
        ];

        return view('pages.agil365.configuracion', [
            'title'    => 'Configuración',
            'settings' => $settings,
            'tab'      => $tab,
        ]);
    }

    public function update(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $tab = $request->get('tab', 'general');

        // ── Validate & Save per tab ────────────────────────────────────────────
        if ($tab === 'general') {
            $validated = $request->validate([
                'app_name'           => 'required|string|max:255',
                'notification_email' => 'required|email|max:255',
                'timezone'           => 'required|string|max:255',
                'language'           => 'required|string|max:10',
                'date_format'        => 'required|string|max:20',
                'items_per_page'     => 'required|integer|min:5|max:100',
            ]);
            Setting::set('app_name',           $validated['app_name']);
            Setting::set('notification_email', $validated['notification_email']);
            Setting::set('timezone',           $validated['timezone']);
            Setting::set('language',           $validated['language']);
            Setting::set('date_format',        $validated['date_format']);
            Setting::set('items_per_page',     $validated['items_per_page'], 'integer');
            Setting::set('maintenance_mode',   $request->boolean('maintenance_mode'), 'boolean');

        } elseif ($tab === 'alertas') {
            $validated = $request->validate([
                'alert_risk_threshold_progress' => 'required|integer|min:1|max:99',
                'alert_risk_threshold_time'     => 'required|integer|min:1|max:99',
                'alert_no_update_days'           => 'required|integer|min:1|max:30',
                'alert_digest_frequency'         => 'required|in:realtime,hourly,daily,weekly',
                'alert_auto_resolve_days'        => 'required|integer|min:0|max:90',
            ]);
            Setting::set('alert_risk_enabled',            $request->boolean('alert_risk_enabled'), 'boolean');
            Setting::set('alert_risk_threshold_progress', $validated['alert_risk_threshold_progress'], 'integer');
            Setting::set('alert_risk_threshold_time',     $validated['alert_risk_threshold_time'], 'integer');
            Setting::set('alert_no_update_days',          $validated['alert_no_update_days'], 'integer');
            Setting::set('alert_email_notifications',     $request->boolean('alert_email_notifications'), 'boolean');
            Setting::set('alert_overdue_projects',        $request->boolean('alert_overdue_projects'), 'boolean');
            Setting::set('alert_overdue_tasks',           $request->boolean('alert_overdue_tasks'), 'boolean');
            Setting::set('alert_digest_frequency',        $validated['alert_digest_frequency']);
            Setting::set('alert_critical_only_email',     $request->boolean('alert_critical_only_email'), 'boolean');
            Setting::set('alert_auto_resolve_days',       $validated['alert_auto_resolve_days'], 'integer');

        } elseif ($tab === 'bonos') {
            $validated = $request->validate([
                'bonus_amount'              => 'required|numeric|min:0',
                'bonus_approval_type'       => 'required|in:auto,gerente,admin',
                'bonus_max_per_month'       => 'required|integer|min:1|max:100',
                'bonus_min_project_progress'=> 'required|integer|min:1|max:100',
                'bonus_expiry_days'         => 'required|integer|min:0|max:365',
                'bonus_currency'            => 'required|in:USD,EUR,MXN,COP,ARS,CLP,PEN,BRL',
            ]);
            Setting::set('bonus_enabled',               $request->boolean('bonus_enabled'), 'boolean');
            Setting::set('bonus_amount',                $validated['bonus_amount'], 'integer');
            Setting::set('bonus_approval_type',         $validated['bonus_approval_type']);
            Setting::set('bonus_max_per_month',         $validated['bonus_max_per_month'], 'integer');
            Setting::set('bonus_require_on_time',       $request->boolean('bonus_require_on_time'), 'boolean');
            Setting::set('bonus_min_project_progress',  $validated['bonus_min_project_progress'], 'integer');
            Setting::set('bonus_expiry_days',           $validated['bonus_expiry_days'], 'integer');
            Setting::set('bonus_currency',              $validated['bonus_currency']);
            Setting::set('bonus_notify_engineer',       $request->boolean('bonus_notify_engineer'), 'boolean');
            Setting::set('bonus_notify_admin',          $request->boolean('bonus_notify_admin'), 'boolean');
        }

        return redirect()->route('configuracion', ['tab' => $tab])
            ->with('success', 'Configuración guardada correctamente.');
    }
}
