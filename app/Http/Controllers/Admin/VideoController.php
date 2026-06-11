<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Http\Requests\Admin\UpdateVideoRequest;
use App\Models\Video;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Gate;

class VideoController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Video::class);

        $videos = Video::orderBy('sort_order')->orderByDesc('created_at')->paginate(15);

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        Gate::authorize('create', Video::class);

        return view('admin.videos.create');
    }

    public function store(StoreVideoRequest $request)
    {
        Gate::authorize('create', Video::class);

        $video = Video::create($request->safe()->only([
            'youtube_id', 'title', 'description', 'points_reward', 'is_active', 'sort_order',
        ]));

        AuditLogger::log('CREATE_VIDEO', "Admin menambahkan video: \"{$video->title}\" (YouTube: {$video->youtube_id})");

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(Video $video)
    {
        Gate::authorize('update', $video);

        return view('admin.videos.edit', compact('video'));
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        Gate::authorize('update', $video);

        $video->update($request->safe()->only([
            'youtube_id', 'title', 'description', 'points_reward', 'is_active', 'sort_order',
        ]));

        AuditLogger::log('UPDATE_VIDEO', "Admin memperbarui video ID {$video->id}: \"{$video->title}\"");

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Video $video)
    {
        Gate::authorize('delete', $video);

        $title = $video->title;
        $video->delete();   // Soft delete — cascadeOnDelete on UserVideoClaim still handles hard-delete scenario

        AuditLogger::log('DELETE_VIDEO', "Admin menghapus video: \"{$title}\"");

        return redirect()->route('admin.videos.index')
            ->with('success', "Video \"{$title}\" berhasil dihapus.");
    }
}
