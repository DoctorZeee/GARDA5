<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\PlantResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Individual user monitoring & management for admins.
 *
 * Provides drill-down into a single user's complete health and
 * plant data — the "Filament-style" detail page the prompt requested.
 */
class UserDetailController extends Controller
{
    public function __construct(private readonly PlantResetService $plantReset)
    {
    }

    /**
     * Show the full detail profile for a specific user.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        // Eager-load with scoped relationships
        $user->load(['wilayah', 'point']);

        // ── Health Logs (paginated, most recent first) ──────────────────────
        $healthLogs = HealthLog::where('user_id', $user->id)
            ->orderByDesc('tanggal_input')
            ->paginate(12, pageName: 'logPage');

        // ── Monthly aggregates for the chart (last 6 months) ───────────────
        $monthlyStats = HealthLog::where('user_id', $user->id)
            ->where('tanggal_input', '>=', now()->subMonths(6)->toDateString())
            ->select(
                DB::raw(DB::getDriverName() === 'sqlite' ? "strftime('%Y-%m', tanggal_input) as month" : "DATE_FORMAT(tanggal_input, '%Y-%m') as month"),
                DB::raw('count(*) as log_count'),
                DB::raw('avg(berat_badan) as avg_weight')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Blood pressure trend (last 30 records) ─────────────────────────
        $bpTrend = HealthLog::where('user_id', $user->id)
            ->whereNotNull('tekanan_darah')
            ->orderByDesc('tanggal_input')
            ->take(30)
            ->get(['tanggal_input', 'tekanan_darah'])
            ->reverse()
            ->values();

        // ── Hypertension distribution ─────────────────────────────────────
        $hyperDist = HealthLog::where('user_id', $user->id)
            ->select('status_hipertensi', DB::raw('count(*) as total'))
            ->groupBy('status_hipertensi')
            ->pluck('total', 'status_hipertensi');

        // ── Plant reset history ────────────────────────────────────────────
        $resetHistory = $this->plantReset->getResetHistory($user);

        // ── Recent audit logs for this user ───────────────────────────────
        $recentAudit = AuditLogger::recent(limit: 10)
            ->filter(fn ($l) => $l->user_id === $user->id)
            ->values();

        // ── Anomalies / warnings ──────────────────────────────────────────
        $latestLog = $healthLogs->first();
        $anomalies = $this->detectAnomalies($user, $latestLog);

        return view('admin.users.detail', compact(
            'user',
            'healthLogs',
            'monthlyStats',
            'bpTrend',
            'hyperDist',
            'resetHistory',
            'recentAudit',
            'latestLog',
            'anomalies',
        ));
    }

    /**
     * Execute a hard reset of the user's plant data.
     */
    public function hardReset(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $request->validate([
            'reason'  => ['nullable', 'string', 'max:500'],
            'confirm' => ['required', 'accepted'],
        ], [
            'confirm.accepted' => 'Anda harus mencentang kotak konfirmasi untuk melanjutkan reset.',
        ]);

        // Auth::user() returns Authenticatable; findOrFail gives us the concrete
        // App\Models\User type that Intelephense and static analysers can resolve.
        $admin = User::findOrFail(Auth::id());

        $result = $this->plantReset->hardReset(
            targetUser: $user,
            adminUser:  $admin,
            reason:     $request->input('reason'),
        );

        $flash = $result['success'] ? 'success' : 'error';

        return redirect()->route('admin.users.detail', $user)
            ->with($flash, $result['message']);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function detectAnomalies(User $user, ?HealthLog $latest): array
    {
        $warnings = [];

        if (! $latest) {
            $warnings[] = [
                'type'    => 'warning',
                'icon'    => 'fa-clock',
                'message' => 'Pengguna belum pernah mencatat data kesehatan.',
            ];
            return $warnings;
        }

        // ── No log in last 7 days ─────────────────────────────────────────
        if ($latest->tanggal_input->lt(now()->subDays(7))) {
            $days = $latest->tanggal_input->diffInDays(now());
            $warnings[] = [
                'type'    => 'warning',
                'icon'    => 'fa-calendar-xmark',
                'message' => "Tidak ada log selama {$days} hari terakhir.",
            ];
        }

        // ── Hypertension flag ─────────────────────────────────────────────
        if (in_array($latest->status_hipertensi, ['Sedang', 'Berat'])) {
            $warnings[] = [
                'type'    => 'danger',
                'icon'    => 'fa-heart-circle-exclamation',
                'message' => "Status hipertensi terakhir: {$latest->status_hipertensi} — perlu perhatian medis.",
            ];
        }

        // ── High sodium ───────────────────────────────────────────────────
        if ($latest->konsumsi_garam === 'more') {
            $warnings[] = [
                'type'    => 'warning',
                'icon'    => 'fa-salt-shaker',
                'message' => 'Konsumsi garam berlebih pada catatan terakhir.',
            ];
        }

        // ── Abnormal blood pressure ───────────────────────────────────────
        if ($latest->tekanan_darah) {
            $parts = explode('/', $latest->tekanan_darah, 2);
            $systolic  = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : 0;
            $diastolic = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 0;

            if ($systolic > 160 || $diastolic > 100) {
                $warnings[] = [
                    'type'    => 'danger',
                    'icon'    => 'fa-gauge-high',
                    'message' => "Tekanan darah sangat tinggi: {$latest->tekanan_darah} mmHg.",
                ];
            }
        }

        return $warnings;
    }
}