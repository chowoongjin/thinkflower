<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $option = (string) $request->input('option', 'subject');
        $keyword = trim((string) $request->input('keyword', ''));
        $now = now();

        $query = Announcement::query()
            ->where('is_active', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', $now);
            });

        if ($keyword !== '') {
            if ($option === 'content') {
                $query->where('content', 'like', '%' . $keyword . '%');
            } else {
                $query->where('title', 'like', '%' . $keyword . '%');
            }
        }

        $announcements = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.announcement', [
            'announcements' => $announcements,
            'option' => $option,
            'keyword' => $keyword,
        ]);
    }

    public function show(Request $request, Announcement $announcement)
    {
        $now = now();

        abort_unless(
            $announcement->is_active
            && (is_null($announcement->start_at) || $announcement->start_at <= $now)
            && (is_null($announcement->end_at) || $announcement->end_at >= $now),
            404
        );

        $announcement->load('creator');

        $viewSessionKey = 'announcement_viewed_' . $announcement->id;

        if (!$request->session()->has($viewSessionKey)) {
            $announcement->increment('view_count');
            $request->session()->put($viewSessionKey, true);
            $announcement->refresh();
        }

        $prevAnnouncement = Announcement::query()
            ->where('is_active', 1)
            ->where('id', '>', $announcement->id)
            ->orderBy('id')
            ->first();

        $nextAnnouncement = Announcement::query()
            ->where('is_active', 1)
            ->where('id', '<', $announcement->id)
            ->orderByDesc('id')
            ->first();

        return view('pages.announcement-view', [
            'announcement' => $announcement,
            'prevAnnouncement' => $prevAnnouncement,
            'nextAnnouncement' => $nextAnnouncement,
        ]);
    }
}
