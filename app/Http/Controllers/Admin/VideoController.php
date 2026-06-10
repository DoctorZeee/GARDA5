<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Http\Requests\Admin\UpdateVideoRequest;
use App\Models\Video;
use App\Services\AuditLogger;

class VideoController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index()
    {
        $videos = Video::orderBy('sort_order')->orderByDesc('created_at')->paginate(15);

        return view('admin.videos.index', compact('videos'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.videos.create');
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(StoreVideoRequest $request)
    {
        $video = Video::create($request->safe()->only([
            'youtube_id', 'title', 'description', 'points_reward', 'is_active', 'sort_order',
        ]));

        AuditLogger::log('CREATE_VIDEO', "Admin menambahkan video: \"{$video->title}\" (ID YouTube: {$video->youtube_id})");

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video berhasil ditambahkan.');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $video->update($request->safe()->only([
            'youtube_id', 'title', 'description', 'points_reward', 'is_active', 'sort_order',
        ]));

        AuditLogger::log('UPDATE_VIDEO', "Admin memperbarui video ID {$video->id}: \"{$video->title}\"");

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video berhasil diperbarui.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(Video $video)
    {
        $title = $video->title;
        $video->delete();   // cascadeOnDelete di user_video_claims akan hapus claims terkait

        AuditLogger::log('DELETE_VIDEO', "Admin menghapus video: \"{$title}\"");

        return redirect()
            ->route('admin.videos.index')
            ->with('success', "Video \"{$title}\" berhasil dihapus.");
    }
}
