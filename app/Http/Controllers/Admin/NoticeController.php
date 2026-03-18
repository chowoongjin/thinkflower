<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
{
    public function index()
    {
        $generalNotices = Notice::query()
            ->where('category', 'general')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $specialNotices = Notice::query()
            ->where('category', 'special')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $generalRows = $this->buildRows($generalNotices, 3, false);
        $specialRows = $this->buildRows($specialNotices, 3, true);

        return view('admin.notice', compact('generalRows', 'specialRows'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'general' => ['nullable', 'array'],
            'general.*.id' => ['nullable', 'integer'],
            'general.*.title' => ['nullable', 'string', 'max:255'],

            'special' => ['nullable', 'array'],
            'special.*.id' => ['nullable', 'integer'],
            'special.*.title' => ['nullable', 'string', 'max:255'],
            'special.*.is_pinned' => ['nullable', 'in:0,1'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            $this->syncCategory(
                category: 'general',
                rows: collect($validated['general'] ?? []),
                userId: $user?->id,
                supportsPinned: false
            );

            $this->syncCategory(
                category: 'special',
                rows: collect($validated['special'] ?? []),
                userId: $user?->id,
                supportsPinned: true
            );
        });

        return redirect()
            ->route('admin.notice.index')
            ->with('success', '공지사항이 저장되었습니다.');
    }

    protected function syncCategory(string $category, Collection $rows, ?int $userId, bool $supportsPinned): void
    {
        $existing = Notice::query()
            ->where('category', $category)
            ->get()
            ->keyBy('id');

        $keptIds = [];

        foreach ($rows->values() as $index => $row) {
            $id = isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null;
            $title = trim((string)($row['title'] ?? ''));

            if ($title === '') {
                if ($id && $existing->has($id)) {
                    $existing[$id]->delete();
                }
                continue;
            }

            $payload = [
                'category' => $category,
                'title' => $title,
                'is_pinned' => $supportsPinned ? (int)($row['is_pinned'] ?? 0) : 0,
                'is_active' => 1,
                'sort_order' => $index + 1,
                'starts_at' => null,
                'ends_at' => null,
                'updated_by' => $userId,
            ];

            if ($id && $existing->has($id)) {
                $notice = $existing[$id];
                $notice->fill($payload);
                $notice->save();
                $keptIds[] = $notice->id;
            } else {
                $payload['created_by'] = $userId;
                $notice = Notice::create($payload);
                $keptIds[] = $notice->id;
            }
        }

        Notice::query()
            ->where('category', $category)
            ->when(!empty($keptIds), function ($query) use ($keptIds) {
                $query->whereNotIn('id', $keptIds);
            })
            ->when(empty($keptIds), function ($query) {
                return $query;
            })
            ->delete();
    }

    protected function buildRows(Collection $notices, int $count, bool $supportsPinned): array
    {
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $notice = $notices[$i] ?? null;

            $rows[] = [
                'id' => $notice?->id,
                'title' => $notice?->title ?? '',
                'is_pinned' => $supportsPinned ? (int)($notice?->is_pinned ?? 0) : 0,
            ];
        }

        return $rows;
    }
}
