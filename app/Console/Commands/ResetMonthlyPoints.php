<?php

namespace App\Console\Commands;

use App\Models\UserPoint;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Monthly reset of plant growth (total_leaves) for all users.
 *
 * Runs on the 1st of every month at 00:05 WIB via the scheduler.
 * Preserves total_points (lifetime) and only resets total_leaves (monthly visual).
 *
 * Usage (manual):
 *   php artisan garda:reset-monthly-points
 *
 * Scheduled in bootstrap/app.php:
 *   Schedule::command('garda:reset-monthly-points')->monthlyOn(1, '00:05');
 */
class ResetMonthlyPoints extends Command
{
    protected $signature = 'garda:reset-monthly-points
                            {--dry-run : Preview which records would be reset without writing}';

    protected $description = 'Reset monthly plant leaves (total_leaves) for all warga on the 1st of each month.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $month    = now()->format('Y-m');

        $this->info("=== GARDA Monthly Plant Reset ({$month}) ===");

        if ($isDryRun) {
            $this->warn('[DRY RUN] No data will be changed.');
        }

        // Count how many records will be affected
        $count = UserPoint::where('total_leaves', '>', 0)->count();
        $this->line("Records with leaves > 0: {$count}");

        if ($count === 0) {
            $this->info('Nothing to reset. All leaves are already 0.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->table(
                ['user_id', 'total_leaves', 'total_points'],
                UserPoint::where('total_leaves', '>', 0)
                    ->select('user_id', 'total_leaves', 'total_points')
                    ->limit(20)
                    ->get()
                    ->toArray()
            );
            return self::SUCCESS;
        }

        // Confirm in production
        if (app()->isProduction() && ! $this->confirm("Reset leaves for {$count} users? This cannot be undone.", false)) {
            $this->line('Cancelled.');
            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($count, $month) {
                // Archive snapshot BEFORE reset (keeps historical records)
                DB::table('monthly_point_snapshots')->insert(
                    UserPoint::where('total_leaves', '>', 0)
                        ->get(['user_id', 'total_points', 'total_leaves', 'checkin_streak', 'checkin_count'])
                        ->map(fn ($r) => [
                            'user_id'        => $r->user_id,
                            'month'          => now()->subDay()->format('Y-m'),   // last month
                            'total_points'   => $r->total_points,
                            'total_leaves'   => $r->total_leaves,
                            'checkin_streak' => $r->checkin_streak,
                            'checkin_count'  => $r->checkin_count,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ])
                        ->toArray()
                );

                // Reset only total_leaves; preserve total_points (lifetime accumulation)
                UserPoint::where('total_leaves', '>', 0)
                    ->update(['total_leaves' => 0]);
            });

            AuditLogger::log(
                'MONTHLY_RESET',
                "Automatic monthly plant reset for month {$month}: {$count} users reset.",
                userId: null  // System action
            );

            $this->info("✅ Reset complete. {$count} user(s) had their leaves reset to 0.");
            Log::info("Monthly plant reset completed", ['month' => $month, 'count' => $count]);

        } catch (\Throwable $e) {
            $this->error('Reset failed: ' . $e->getMessage());
            Log::error('Monthly plant reset FAILED', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
