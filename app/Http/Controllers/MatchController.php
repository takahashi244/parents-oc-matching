<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\OCEvent;
use App\Models\School;
use App\Support\Analytics;
use Carbon\Carbon;

class MatchController extends Controller
{
    public function match(Request $request)
    {
        $tags            = $request->input('tags', []);               // 興味タグ
        $subjects        = $request->input('subjects', []);           // 好き科目（基本8）
        $subjectsDetail  = $request->input('subjects_detail', []);    // 細分（社会/理科）
        $hero            = $request->input('hero');                   // 尊敬人物（任意）

        \App\Support\Analytics::track(
            'interest_submit',
            [
                'tag_count'     => count($tags),
                'subject_count' => count($subjects) + count($subjectsDetail),
                'has_hero'      => !empty($hero),
            ],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );

        // DBから学科と直近60日のOCを取得
        $depts  = Department::query()->get(['dept_id', 'dept_name', 'school_id', 'tags']);
        $schools = School::query()->pluck('school_name', 'school_id');
        $events = OCEvent::query()
            ->whereDate('date', '>=', now()->subDay())
            ->whereDate('date', '<=', now()->addDays(60))
            ->get(['ocev_id', 'dept_id', 'date', 'place', 'reservation_url'])
            ->groupBy('dept_id');

        $scored = [];
        foreach ($depts as $d) {
            $deptTags   = (array)($d->tags ?? []);
            $tag_match  = min(2, count(array_intersect($tags, $deptTags)));

            // 科目マップ（簡易）
            $subjectMap = [
                '英語' => ['english_global'],
                '国語' => ['edu_psych', 'history_social'],
                '数学' => ['programming', 'math_data'],
                '理科' => ['science', 'medical'],
                '社会' => ['history_social', 'business', 'english_global'],
                '芸術' => ['design_art', 'media', 'music'],
                '情報' => ['programming', 'media'],
                '保健体育' => ['sports', 'medical'],
                '地理' => ['history_social'],
                '歴史' => ['history_social'],
                '公共' => ['history_social', 'business'],
                '物理' => ['science', 'programming'],
                '化学' => ['science', 'medical'],
                '生物' => ['science', 'medical'],
            ];
            $subject_match = 0;
            foreach (array_unique(array_merge($subjects, $subjectsDetail)) as $sj) {
                if (!empty(array_intersect($subjectMap[$sj] ?? [], $deptTags))) {
                    $subject_match = 1;
                    break;
                }
            }

            // ヒーロー簡易マップ
            $hero_match = 0;
            if ($hero) {
                $map = [
                    '起業' => 'business',
                    '起業家' => 'business',
                    'デザイナー' => 'design_art',
                    'エンジニア' => 'programming',
                    '研究者' => 'science',
                    'アーティスト' => 'music'
                ];
                foreach ($map as $kw => $tg) {
                    if (mb_strpos($hero, $kw) !== false && in_array($tg, $deptTags, true)) {
                        $hero_match = 1;
                        break;
                    }
                }
            }

            // 直近OCボーナス
            $bonus_oc = 0;
            $ocLabel = null;
            $ocev_id = null;
            if ($events->has($d->dept_id)) {
                $nearest = $events[$d->dept_id]->sortBy('date')->first();
                $ocev_id = $nearest->ocev_id;
                $ocLabel = Carbon::parse($nearest->date)->isoFormat('MM/DD');
                $days    = now()->diffInDays($nearest->date, false);
                $bonus_oc = $days <= 30 ? 4 : ($days <= 60 ? 2 : 0);
            }

            $score = 10 * $tag_match + 6 * $subject_match + 5 * $hero_match + $bonus_oc;

            // 理由（最低3つ）
            $reasons = [];
            if ($tag_match > 0) {
                $reasons[] = ($tags[0] ?? '興味') . " × 学びの環境";
            }
            if ($ocLabel) {
                $reasons[] = "直近{$ocLabel}にOCあり";
            }
            while (count($reasons) < 3) {
                $reasons[] = "学びの雰囲気が合いそう";
            }

            $scored[] = [
                'school_name' => $schools[$d->school_id] ?? '学校名未登録',
                'dept_name'   => $d->dept_name,
                'ocev_id'     => $ocev_id,
                'score'       => $score,
                'reasons'     => array_slice($reasons, 0, 3),
                'detail_url'  => $ocev_id ? url("/oc/{$ocev_id}") : '#',
            ];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        $candidates = array_slice($scored, 0, 5);

        // 計測
        if (auth()->check()) {
            \Illuminate\Support\Facades\DB::table('matches')->insert([
                'user_id'    => auth()->id(),
                'candidates' => json_encode($candidates, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
            ]);
        }

        session()->put('match.last_candidates', $candidates);

        \App\Support\Analytics::track(
            'match_view',
            [
                'candidate_count' => count($candidates),
                'top_tag'         => $tags[0] ?? null,
            ],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );

        // HTMLで返す
        return view('match-results', compact('candidates'));
    }
}
