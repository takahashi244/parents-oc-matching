<?php
namespace App\Http\Controllers;

use App\Models\OCEvent;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OCEventController extends Controller
{
    public function show(string $id)
    {
        $ev = OCEvent::findOrFail($id);

        $deptMeta = DB::table('departments as d')
            ->join('schools as s', 's.school_id', '=', 'd.school_id')
            ->select('d.dept_name', 's.school_name')
            ->where('d.dept_id', $ev->dept_id)
            ->first();

        $reasons = [];
        $parentRatings = [];
        $childRatings = [];
        if (auth()->check()) {
            $match = DB::table('matches')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->first();

            if ($match) {
                $candidates = json_decode($match->candidates, true) ?? [];
                foreach ($candidates as $candidate) {
                    if (($candidate['ocev_id'] ?? null) === $id) {
                        $reasons = $candidate['reasons'] ?? [];
                        break;
                    }
                }
            }
        }

        if (empty($reasons)) {
            $sessionCandidates = session('match.last_candidates', []);
            foreach ($sessionCandidates as $candidate) {
                if (($candidate['ocev_id'] ?? null) === $id) {
                    $reasons = $candidate['reasons'] ?? [];
                    break;
                }
            }
        }

        $parentMemo = null;
        $parentMemoRecordedAt = null;
        if (auth()->check()) {
            $parentMemo = DB::table('oc_memos')
                ->where('ocev_id', $id)
                ->where('role', 'parent')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->first();

            if ($parentMemo) {
                $parentRatings = json_decode($parentMemo->ratings ?? '[]', true) ?? [];
                if (!empty($parentMemo->created_at)) {
                    $parentMemoRecordedAt = Carbon::parse($parentMemo->created_at)->format('Y/m/d H:i');
                }
            }
        }

        $childMemo = null;
        $childMemoRecordedAt = null;
        if (auth()->check()) {
            $childMemo = DB::table('oc_memos')
                ->where('ocev_id', $id)
                ->where('role', 'student')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->first();

            if ($childMemo) {
                $childRatings = json_decode($childMemo->ratings ?? '[]', true) ?? [];
                if (!empty($childMemo->created_at)) {
                    $childMemoRecordedAt = Carbon::parse($childMemo->created_at)->format('Y/m/d H:i');
                }
            }
        }

        $parentLabels = [];
        foreach ($this->parentChecklist() as $item) {
            $parentLabels[$item['id']] = $item['label'];
        }

        $childLabels = [];
        foreach ($this->childChecklist() as $item) {
            $childLabels[$item['id']] = $item['label'];
        }

        Analytics::track(
            'oc_detail_view',
            ['ocev_id' => $id, 'dept_id' => $ev->dept_id],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );
        return view('oc/detail', [
            'ev'            => $ev,
            'reasons'       => $reasons,
            'parentRatings' => $parentRatings,
            'childRatings'  => $childRatings,
            'parentLabels'  => $parentLabels,
            'childLabels'   => $childLabels,
            'parentMemo'    => $parentMemo,
            'childMemo'     => $childMemo,
            'parentMemoRecordedAt' => $parentMemoRecordedAt,
            'childMemoRecordedAt'  => $childMemoRecordedAt,
            'deptName'      => $deptMeta->dept_name ?? null,
            'schoolName'    => $deptMeta->school_name ?? null,
        ]);
    }

    public function showParentMemo(string $id)
    {
        $ev = OCEvent::findOrFail($id);

        $draft = session('review_draft');
        $prefill = null;

        if ($draft && ($draft['ocev_id'] ?? null) === $id && ($draft['user_id'] ?? null) === auth()->id()) {
            $prefill = [
                'ratings'        => $draft['ratings'] ?? [],
                'overall_rating' => $draft['overall_rating'] ?? null,
                'good_text'      => $draft['good_text'] ?? null,
                'concern_text'   => $draft['concern_text'] ?? null,
            ];
        }

        return view('oc/memo-parent', [
            'ev'        => $ev,
            'checklist' => $this->parentChecklist(),
            'prefill'   => $prefill,
        ]);
    }

    public function storeParentMemo(string $id, Request $request)
    {
        $ev = OCEvent::findOrFail($id);

        $validated = $request->validate([
            'ratings'        => ['required', 'array'],
            'ratings.*'      => ['nullable', 'integer', 'min:1', 'max:5'],
            'overall_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'good_text'      => ['required', 'string', 'max:4000'],
            'concern_text'   => ['required', 'string', 'max:4000'],
            'agree_terms'    => ['accepted'],
        ], [
            'agree_terms.accepted' => '注意事項への同意が必要です。',
        ]);

        $ratings = array_filter(
            $validated['ratings'] ?? [],
            fn($value) => $value !== null && $value !== ''
        );
        $ratings = array_map('intval', $ratings);

        if (count($ratings) === 0) {
            return back()->withErrors(['ratings' => '少なくとも1項目は評価してください。'])->withInput();
        }

        DB::table('oc_memos')->insert([
            'ocev_id'   => $id,
            'user_id'   => auth()->id(),
            'role'      => 'parent',
            'ratings'   => json_encode($ratings, JSON_UNESCAPED_UNICODE),
            'notes'     => null,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        Analytics::track(
            'oc_memo_create',
            [
                'ocev_id' => $id,
                'role'    => 'parent',
                'filled'  => array_keys($ratings),
            ],
            auth()->id(),
            'parent'
        );

        $good    = trim($validated['good_text']);
        $concern = trim($validated['concern_text']);
        $average = round(array_sum($ratings) / count($ratings), 1);

        $checklist = collect($this->parentChecklist())->keyBy('id');
        $scoredItems = collect($ratings)->map(function ($score, $key) use ($checklist) {
            return [
                'id'    => $key,
                'label' => $checklist[$key]['label'] ?? $key,
                'score' => $score,
            ];
        })->values()->all();

        session(['review_draft' => [
            'draft_id'       => (string) Str::uuid(),
            'ocev_id'        => $id,
            'user_id'        => auth()->id(),
            'ratings'        => $ratings,
            'average_rating' => $average,
            'overall_rating' => (int) $validated['overall_rating'],
            'good_text'      => $good,
            'concern_text'   => $concern,
            'scored_items'   => $scoredItems,
        ]]);

        return redirect()->route('oc.review.confirm', ['id' => $id]);
    }

    public function confirmParentReview(string $id)
    {
        $ev = OCEvent::findOrFail($id);
        $draft = session('review_draft');

        if (!$draft || ($draft['ocev_id'] ?? null) !== $id || ($draft['user_id'] ?? null) !== auth()->id()) {
            return redirect()->route('oc.memo.parent', ['id' => $id])
                ->with('error', 'レビューを確認できる内容がありません。もう一度入力してください。');
        }

        return view('oc/review-confirm', [
            'ev'          => $ev,
            'draft'       => $draft,
            'scoredItems' => $draft['scored_items'] ?? [],
        ]);
    }

    public function publishParentReview(string $id, Request $request)
    {
        $draft = session('review_draft');

        if (!$draft || ($draft['ocev_id'] ?? null) !== $id || ($draft['user_id'] ?? null) !== auth()->id()) {
            return redirect()->route('oc.memo.parent', ['id' => $id])
                ->with('error', '公開できるレビューが見つかりませんでした。もう一度入力してください。');
        }

        $revId = Str::upper(Str::random(8));

        DB::table('reviews')->insert([
            'rev_id'       => $revId,
            'ocev_id'      => $id,
            'user_id'      => auth()->id(),
            'author_role'  => 'parent',
            'rating'       => $draft['overall_rating'],
            'pros'         => json_encode($draft['good_text'] ? [$draft['good_text']] : [], JSON_UNESCAPED_UNICODE),
            'cons'         => json_encode($draft['concern_text'] ? [$draft['concern_text']] : [], JSON_UNESCAPED_UNICODE),
            'notes'        => null,
            'is_published' => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Analytics::track(
            'review_publish',
            [
                'ocev_id'   => $id,
                'rating'    => $draft['overall_rating'],
                'published' => true,
                'average'   => $draft['average_rating'],
            ],
            auth()->id(),
            'parent'
        );

        session()->forget('review_draft');

        return redirect()->route('reviews.show', ['rev_id' => $revId])
            ->with('ok', 'レビューを公開しました。ありがとうございました。');
    }

    public function showChildMemo(string $id)
    {
        $ev = OCEvent::findOrFail($id);

        return view('oc/memo-child', [
            'ev'        => $ev,
            'checklist' => $this->childChecklist(),
        ]);
    }

    public function storeChildMemo(string $id, Request $request)
    {
        $validated = $request->validate([
            'ratings'     => ['required', 'array'],
            'ratings.*'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'agree_terms' => ['accepted'],
        ], [
            'agree_terms.accepted' => '注意事項への同意が必要です。',
        ]);

        $ratings = array_filter(
            $validated['ratings'] ?? [],
            fn($value) => $value !== null && $value !== ''
        );
        $ratings = array_map('intval', $ratings);

        if (count($ratings) === 0) {
            return back()->withErrors(['ratings' => '少なくとも1項目は評価してください。'])->withInput();
        }

        DB::table('oc_memos')->insert([
            'ocev_id'   => $id,
            'user_id'   => auth()->id(),
            'role'      => 'student',
            'ratings'   => json_encode($ratings, JSON_UNESCAPED_UNICODE),
            'notes'     => null,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        Analytics::track(
            'oc_memo_create',
            [
                'ocev_id' => $id,
                'role'    => 'student',
                'filled'  => array_keys($ratings),
            ],
            auth()->id(),
            'student'
        );

        return redirect()->route('oc.show', ['id' => $id])
            ->with('ok', '子ども用メモを保存しました。');
    }

    public function reserve(string $id)
    {
        $ev = OCEvent::findOrFail($id);

        if (!$ev->reservation_url) {
            return redirect()->route('oc.show', ['id' => $id])
                ->with('error', 'このOCには予約リンクが設定されていません。');
        }

        Analytics::track(
            'reserve_click',
            ['ocev_id' => $id, 'dept_id' => $ev->dept_id, 'dest_domain' => parse_url($ev->reservation_url, PHP_URL_HOST)],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );

        return redirect()->away($ev->reservation_url);
    }

    private function parentChecklist(): array
    {
        return [
            ['id' => 'P01', 'label' => 'アクセス/通学負担', 'description' => '片道時間・乗換回数の負荷'],
        ['id' => 'P02', 'label' => '学部学科の説明の明確さ', 'description' => '入試方式・カリキュラムの分かりやすさ'],
        ['id' => 'P03', 'label' => '設備の適合', 'description' => 'スタジオ/実験室/PC・ソフト等の充実度'],
        ['id' => 'P04', 'label' => '学生・教職員の対応', 'description' => '質問のしやすさ/雰囲気/誠実さ'],
        ['id' => 'P05', 'label' => 'キャリア/進路情報', 'description' => '就職/編入/留学/資格の説明'],
        ['id' => 'P06', 'label' => '費用の透明性', 'description' => '学費/諸経費/奨学金の説明明確さ'],
        ['id' => 'P07', 'label' => '安全・生活面', 'description' => 'キャンパス環境/治安/住まい案内'],
        ['id' => 'P08', 'label' => 'OC運営品質', 'description' => '受付/導線/時間管理/混雑の少なさ'],
        ['id' => 'P09', 'label' => '期待値整合', 'description' => '広報・サイトと当日のギャップ'],
        ['id' => 'P10', 'label' => '興味タグとの繋がり', 'description' => '事前診断の興味タグに合うか'],
        ];
    }

    private function childChecklist(): array
    {
        return [
            ['id' => 'C01', 'label' => '学びのワクワク度', 'description' => '体験授業/デモの面白さ'],
            ['id' => 'C02', 'label' => '「自分ごと」感', 'description' => 'ここで学ぶ自分が想像できる'],
            ['id' => 'C03', 'label' => '難易度フィット', 'description' => 'ついていけそう/物足りないのバランス'],
            ['id' => 'C04', 'label' => '仲間・先輩の雰囲気', 'description' => '話しやすさ/価値観の近さ'],
            ['id' => 'C05', 'label' => 'サークル・課外', 'description' => '興味に合う活動の有無'],
            ['id' => 'C06', 'label' => '設備の使いやすさ', 'description' => '触れた/触れない/操作性'],
            ['id' => 'C07', 'label' => 'キャンパス快適度', 'description' => '広さ/休憩/食堂/トイレ'],
            ['id' => 'C08', 'label' => '通学イメージ', 'description' => '通える・通い続けたいと感じる'],
            ['id' => 'C09', 'label' => '不安の解消度', 'description' => '入試/単位/人間関係などの不安が減った'],
            ['id' => 'C10', 'label' => '推しポイント', 'description' => '一番良かった点（1つ）'],
        ];
    }
}
