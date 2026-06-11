<?php

namespace App\Http\Controllers\User;

use App\DTOs\HealthLogData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreHealthLogRequest;
use App\Models\HealthLog;
use App\Services\PointService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HealthLogController extends Controller
{
    public function __construct(private readonly PointService $pointService)
    {
    }

    public function store(StoreHealthLogRequest $request)
    {
        Gate::authorize('create', HealthLog::class);

        $user = auth()->user();

        // Prevent duplicate log within the same day
        if (HealthLog::where('user_id', $user->id)
                     ->whereDate('tanggal_input', Carbon::today())
                     ->exists()) {
            return back()->with('error', 'Anda sudah mencatat data kesehatan hari ini.');
        }

        $dto = HealthLogData::fromRequest($request, $user->id);

        DB::transaction(function () use ($dto, $user): void {
            HealthLog::create($dto->toArray());

            // Award health log point — does NOT open a new transaction
            $this->pointService->awardHealthLogPoint($user);
        });

        return back()->with('success', 'Data Kesehatan Berhasil Disimpan! +1 Daun tumbuh di pohon Anda. 🌿');
    }
}
