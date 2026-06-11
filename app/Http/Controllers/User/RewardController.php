<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\AuditLogger;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(private readonly PointService $pointService)
    {
    }

    /**
     * Daily check-in.
     */
    public function checkin(Request $request): RedirectResponse
    {
        $user   = auth()->user();
        $result = $this->pointService->checkin($user);

        if ($result['success']) {
            AuditLogger::log(
                'CHECKIN',
                "User {$user->id} check-in harian +1 poin (streak: {$result['streak']})"
            );
        }

        $flashKey = $result['success'] ? 'success' : 'error';

        return back()->with($flashKey, $result['message']);
    }

    /**
     * Claim reward after watching a video.
     */
    public function claimVideo(Request $request, Video $video): RedirectResponse
    {
        $user   = auth()->user();
        $result = $this->pointService->claimVideo($user, $video);

        if ($result['success']) {
            AuditLogger::log(
                'CLAIM_VIDEO',
                "User {$user->id} klaim video {$video->id} \"{$video->title}\" +{$video->points_reward} poin"
            );
        }

        $flashKey = $result['success'] ? 'success' : 'error';

        return back()->with($flashKey, $result['message']);
    }
}
