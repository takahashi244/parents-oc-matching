<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Support\ModerationLog;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'prefecture'  => $request->query('prefecture'),
            'school_type' => $request->query('school_type'),
            'tag'         => $request->query('tag'),
            'sort'        => $request->query('sort', 'rating'),
        ];

        $tagDictionary = collect(config('tag_labels', []));
        if ($tagDictionary->isEmpty()) {
            $tagDictionary = collect(require base_path('config/tag_labels.php'));
        }
        $tagDictionary = $tagDictionary->mapWithKeys(function ($label, $code) {
            $normalized = Str::of($code)->trim()->lower()->toString();
            return [$normalized => $label];
        });

        $prefectureOptions = DB::table('schools')
            ->select('prefecture')
            ->whereNotNull('prefecture')
            ->distinct()
            ->orderBy('prefecture')
            ->pluck('prefecture')
            ->values();

        $schoolTypeLabels = [
            'university' => '大学・短大',
            'vocational' => '専門学校',
        ];

        $tagOptionsRaw = DB::table('departments')
            ->select('tags')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatMap(fn($tags) => collect(explode(';', $tags))->map(fn($tag) => trim($tag))->filter())
            ->unique()
            ->values();

        $tagOptions = $tagOptionsRaw
            ->map(function ($code) use ($tagDictionary) {
                $normalized = Str::of($code)->trim()->lower()->replace(' ', '_')->replace('-', '_')->toString();
                $label = $tagDictionary->get($normalized);
                if (!$label) {
                    $label = Str::of($code)->replace('_', ' ')->replace('-', ' ')->headline();
                }

                return ['code' => $code, 'label' => $label];
            })
            ->sortBy('label')
            ->values();

        $query = DB::table('reviews as r')
            ->join('oc_events as e', 'e.ocev_id', '=', 'r.ocev_id')
            ->join('departments as d', 'd.dept_id', '=', 'e.dept_id')
            ->join('schools as s', 's.school_id', '=', 'd.school_id')
            ->where('r.is_published', true)
            ->select([
                's.school_id',
                's.school_name',
                's.prefecture',
                's.school_type',
                DB::raw('AVG(r.rating) as avg_rating'),
                DB::raw('COUNT(DISTINCT r.rev_id) as review_count'),
                DB::raw('MAX(r.created_at) as last_review_at'),
            ])
            ->groupBy('s.school_id', 's.school_name', 's.prefecture', 's.school_type');

        if ($filters['prefecture']) {
            $query->where('s.prefecture', $filters['prefecture']);
        }

        if ($filters['school_type']) {
            $query->where('s.school_type', $filters['school_type']);
        }

        if ($filters['tag']) {
            $query->where(function ($q) use ($filters) {
                $q->where('d.tags', 'like', '%' . $filters['tag'] . '%');
            });
        }

        switch ($filters['sort']) {
            case 'count':
                $query->orderByDesc('review_count')->orderByDesc('last_review_at');
                break;
            case 'recent':
                $query->orderByDesc('last_review_at')->orderByDesc('review_count');
                break;
            default:
                $filters['sort'] = 'rating';
                $query->orderByDesc('avg_rating')->orderByDesc('review_count');
                break;
        }

        $schools = $query->get();

        $tagMap = DB::table('departments')
            ->select('school_id', 'tags')
            ->whereNotNull('tags')
            ->get()
            ->groupBy('school_id')
            ->map(function ($rows) {
                return collect($rows)
                    ->flatMap(fn($row) => collect(explode(';', (string) $row->tags))->map(fn($tag) => trim($tag)))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            });

        $latestReviews = DB::table('reviews as r')
            ->join('oc_events as e', 'e.ocev_id', '=', 'r.ocev_id')
            ->join('departments as d', 'd.dept_id', '=', 'e.dept_id')
            ->join('schools as s', 's.school_id', '=', 'd.school_id')
            ->where('r.is_published', true)
            ->orderByDesc('r.created_at')
            ->select([
                's.school_id',
                'r.pros',
                'r.cons',
                'r.notes',
            ])
            ->get()
            ->unique('school_id')
            ->keyBy('school_id');

        $parsePoints = function ($value) {
            if (empty($value)) {
                return [];
            }
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return collect($decoded)
                    ->map(fn($item) => is_string($item) ? trim($item) : '')
                    ->filter()
                    ->values()
                    ->all();
            }
            return collect(preg_split('/\r?\n/', $value))
                ->map(fn($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        };

        $schools = $schools->map(function ($row) use ($tagMap, $tagDictionary, $latestReviews, $parsePoints) {
            $row->avg_rating = $row->avg_rating ? round($row->avg_rating, 1) : null;
            $row->last_review_date = $row->last_review_at ? Carbon::parse($row->last_review_at)->format('Y/m/d') : null;
            $row->tag_list = collect($tagMap->get($row->school_id, []))
                ->map(function ($code) use ($tagDictionary) {
                    $normalized = Str::of($code)->trim()->lower()->replace(' ', '_')->replace('-', '_')->toString();
                    $label = $tagDictionary->get($normalized);
                    if (!$label) {
                        logger()->debug('Missing tag label', ['raw' => $code, 'normalized' => $normalized]);
                        $label = Str::of($code)->replace('_', ' ')->replace('-', ' ')->headline();
                    }
                    return $label;
                })
                ->sort()
                ->values()
                ->all();

            $latest = $latestReviews->get($row->school_id);
            if ($latest) {
                $row->latest_review = [
                    'pros'  => $parsePoints($latest->pros),
                    'cons'  => $parsePoints($latest->cons),
                    'notes' => trim($latest->notes ?? ''),
                ];
            } else {
                $row->latest_review = ['pros' => [], 'cons' => [], 'notes' => ''];
            }
            return $row;
        });

        $groupedSchools = $schools
            ->groupBy(fn($row) => $row->prefecture ?: 'エリア未設定')
            ->sortKeys();

        return view('reviews.index', [
            'groupedSchools'    => $groupedSchools,
            'filters'           => $filters,
            'prefectureOptions' => $prefectureOptions,
            'tagOptions'        => $tagOptions,
            'schoolTypeLabels'  => $schoolTypeLabels,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = DB::table('reviews as r')
            ->join('oc_events as e', 'e.ocev_id', '=', 'r.ocev_id')
            ->join('departments as d', 'd.dept_id', '=', 'e.dept_id')
            ->join('schools as s', 's.school_id', '=', 'd.school_id')
            ->orderByDesc('r.created_at')
            ->select([
                'r.rev_id',
                'r.ocev_id',
                'r.author_role',
                'r.rating',
                'r.pros',
                'r.cons',
                'r.notes',
                'r.is_published',
                'r.created_at',
                'd.dept_name',
                's.school_name',
            ]);

        if ($status === 'pending') {
            $query->where('r.is_published', false);
        } elseif ($status === 'published') {
            $query->where('r.is_published', true);
        }

        $items = $query->paginate(20)->withQueryString();

        return view('reviews.admin', [
            'items'  => $items,
            'status' => $status,
        ]);
    }

    public function show(string $revId)
    {
        $review = DB::table('reviews as r')
            ->join('oc_events as e', 'e.ocev_id', '=', 'r.ocev_id')
            ->join('departments as d', 'd.dept_id', '=', 'e.dept_id')
            ->join('schools as s', 's.school_id', '=', 'd.school_id')
            ->select([
                'r.rev_id',
                'r.ocev_id',
                'r.author_role',
                'r.rating',
                'r.pros',
                'r.cons',
                'r.notes',
                'r.created_at',
                's.school_name',
                's.school_id',
                'd.dept_name',
            ])
            ->where('r.rev_id', $revId)
            ->where('r.is_published', true)
            ->first();

        if (!$review) {
            abort(404);
        }

        $pros = array_values(array_filter(json_decode($review->pros ?? '[]', true) ?? []));
        $cons = array_values(array_filter(json_decode($review->cons ?? '[]', true) ?? []));

        return view('reviews.show', [
            'review' => $review,
            'pros'   => $pros,
            'cons'   => $cons,
        ]);
    }

    public function moderate(Request $request, string $revId)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:publish,unpublish,delete'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $review = DB::table('reviews')->where('rev_id', $revId)->first();

        if (!$review) {
            return back()->with('error', '指定のレビューが見つかりません。');
        }

        $action = $validated['action'];

        if ($action === 'delete') {
            DB::table('reviews')->where('rev_id', $revId)->delete();
            $resultMessage = 'レビューを削除しました。';
            ModerationLog::record([
                'review_id' => $revId,
                'action'    => 'delete',
                'reason'    => $validated['reason'] ?? '',
            ]);
        } else {
            $isPublished = $action === 'publish';

            DB::table('reviews')->where('rev_id', $revId)->update([
                'is_published' => $isPublished,
                'updated_at'   => now(),
            ]);

            $resultMessage = $isPublished ? 'レビューを公開状態にしました。' : 'レビューを非公開にしました。';

            ModerationLog::record([
                'review_id' => $revId,
                'action'    => $action,
                'reason'    => $validated['reason'] ?? '',
            ]);
        }

        return back()->with('ok', $resultMessage);
    }


}
