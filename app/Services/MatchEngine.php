<?php

namespace App\Services;

use App\Models\Department;
use App\Models\OCEvent;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Collections\Collection;

class MatchEngine
{
    protected array $preferences;

    public function __construct()
    {
        $this->preferences = config('match_preferences');
    }

    public function buildCandidates(array $payload): array
    {
        $tagContext = $this->calculateTagScores($payload);
        $tagScores  = $tagContext['scores'];
        $ocScores   = $this->buildOcScores($payload['prefecture'] ?? null);

        $departments = Department::query()->get();
        $schools     = School::query()->pluck('school_name', 'school_id');

        $results = [];
        foreach ($departments as $department) {
            $scoreData = $this->scoreDepartment(
                $department,
                $tagScores,
                $ocScores,
                $tagContext['interestReasons'],
                $tagContext['heroReasons'],
                $payload['hero_note'] ?? null
            );

            if ($scoreData === null) {
                continue;
            }

            $results[] = [
                'school_name' => $schools[$department->school_id] ?? '学校名未登録',
                'dept_name'   => $department->dept_name,
                'ocev_id'     => $scoreData['ocev_id'],
                'score'       => $scoreData['score'],
                'reasons'     => $scoreData['reasons'],
                'detail_url'  => $scoreData['ocev_id'] ? url("/oc/{$scoreData['ocev_id']}") : '#',
            ];
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, 5);
    }

    protected function calculateTagScores(array $payload): array
    {
        $scores          = [];
        $interestReasons = [];
        $heroReasons     = [];

        foreach ((array)($payload['interests'] ?? []) as $interestOptionId) {
            $option = $this->findCategoryOption($interestOptionId);
            if (!$option) {
                continue;
            }
            foreach ($option['tags'] as $tag) {
                $scores[$tag] = ($scores[$tag] ?? 0) + 8;
            }
            $interestReasons[] = $option;
        }

        $allSubjects = array_unique(array_merge(
            (array)($payload['subjects'] ?? []),
            (array)($payload['subjects_detail'] ?? [])
        ));

        foreach ($allSubjects as $subject) {
            foreach ($this->preferences['subjects'][$subject] ?? [] as $map) {
                $scores[$map['tag']] = ($scores[$map['tag']] ?? 0) + $map['weight'];
            }
        }

        foreach ((array)($payload['heroes'] ?? []) as $hero) {
            $heroConf = $this->preferences['hero_categories'][$hero] ?? null;
            if (!$heroConf) {
                continue;
            }
            foreach ($heroConf['tags'] as $tag) {
                $scores[$tag] = ($scores[$tag] ?? 0) + 6;
            }
            $heroReasons[] = $heroConf;
        }

        foreach ($scores as $tag => $value) {
            $scores[$tag] = min(20, $value);
        }

        arsort($scores);

        return [
            'scores' => $scores,
            'interestReasons' => $interestReasons,
            'heroReasons' => $heroReasons,
        ];
    }

    protected function buildOcScores(?string $userPrefecture): array
    {
        $events = OCEvent::query()
            ->whereDate('date', '>=', now()->subDays(1))
            ->whereDate('date', '<=', now()->addDays(120))
            ->get()
            ->groupBy('dept_id');

        $scores = [];
        foreach ($events as $deptId => $deptEvents) {
            /** @var Collection $deptEvents */
            $nearest = $deptEvents->sortBy('date')->first();
            $scores[$deptId] = $this->calculateOcScore($nearest, $userPrefecture);
        }

        return $scores;
    }

    protected function calculateOcScore(OCEvent $event, ?string $userPrefecture): array
    {
        $ocConfig = $this->preferences['oc'];
        $date     = Carbon::parse($event->date);
        $daysDiff = now()->diffInDays($date, false);

        $dateScore = 0;
        foreach ($ocConfig['date_score'] as $rule) {
            if ($daysDiff <= $rule['max_days']) {
                $dateScore = $rule['score'];
                break;
            }
        }

        $additional = 0;
        if (!empty($userPrefecture) && $event->place && str_contains($event->place, $userPrefecture)) {
            $additional += $ocConfig['same_prefecture_score'];
        }

        if ($event->format && in_array($event->format, ['online', 'hybrid'], true)) {
            $additional += $ocConfig['online_score'];
        }

        $totalScore = $dateScore + $additional;

        $formatLabel = match ($event->format) {
            'online' => 'オンライン',
            'hybrid' => 'ハイブリッド',
            default  => '対面',
        };

        $tplKey = $event->format === 'online' ? 'online' : 'default';
        $reason = $ocConfig['reason_templates'][$tplKey] ?? $ocConfig['reason_templates']['default'];

        $reason = str_replace(
            [':date', ':format', ':place'],
            [$date->isoFormat('MM/DD'), $formatLabel, $event->place ?? ''],
            $reason
        );

        return [
            'score'   => $totalScore,
            'reason'  => $reason,
            'ocev_id' => $event->ocev_id,
        ];
    }

    protected function scoreDepartment(
        Department $department,
        array $tagScores,
        array $ocScores,
        array $interestReasons,
        array $heroReasons,
        ?string $heroNote
    ): ?array {
        $rawTags = $department->tags;
        if (is_string($rawTags)) {
            $tags = array_values(array_filter(array_map('trim', explode(';', $rawTags))));
        } else {
            $tags = (array) $rawTags;
        }

        if (empty($tags)) {
            return null;
        }

        $score       = 0;
        $tagMessages = $this->preferences['tag_reasons'];
        $learningReasons  = [];
        $interestHighlights = [];
        $heroHighlights     = [];

        foreach ($tags as $tag) {
            if (isset($tagScores[$tag])) {
                $score += $tagScores[$tag];
                if (isset($tagMessages[$tag])) {
                    $learningReasons[] = $tagMessages[$tag];
                }
            }
        }

        foreach ($interestReasons as $interest) {
            if (!empty(array_intersect($interest['tags'], $tags))) {
                $interestHighlights[] = $interest['reason'];
            }
        }

        foreach ($heroReasons as $heroReason) {
            if (!empty(array_intersect($heroReason['tags'], $tags))) {
                $heroHighlights[] = $heroReason['reason'];
            }
        }

        $ocData   = $ocScores[$department->dept_id] ?? null;
        $ocScore  = $ocData['score'] ?? 0;
        $score   += $ocScore;

        if ($score === 0 && $ocScore === 0) {
            $score = 1; // 最低でも候補として表示するためのベーススコア
            $learningReasons[] = '診断結果に近い学科が少なかったため、関連度の高い候補を表示しています。';
        }

        $ocReason = !empty($ocData['reason']) ? [$ocData['reason']] : [];

        $noteReason = [];
        if ($heroNote) {
            $noteReason[] = '憧れに挙げた『' . trim($heroNote) . '』との共通点を見つけやすい学びです。';
        }

        $orderedReasons = array_merge($learningReasons, $interestHighlights, $heroHighlights, $ocReason, $noteReason);
        $uniqueReasons = [];
        foreach ($orderedReasons as $text) {
            $clean = trim($text);
            if ($clean === '') {
                continue;
            }
            $uniqueReasons[$clean] = true;
        }
        $reasons = array_slice(array_keys($uniqueReasons), 0, 5);
        if (count($reasons) < 3) {
            $reasons = array_pad($reasons, 3, '学びの雰囲気が合いそうです。');
        }

        return [
            'score'  => $score,
            'reasons' => $reasons,
            'ocev_id' => $ocData['ocev_id'] ?? null,
        ];
    }

    protected function findCategoryOption(string $optionId): ?array
    {
        foreach ($this->preferences['categories'] as $category) {
            foreach ($category['options'] as $option) {
                if ($option['id'] === $optionId) {
                    return $option;
                }
            }
        }

        return null;
    }
}
