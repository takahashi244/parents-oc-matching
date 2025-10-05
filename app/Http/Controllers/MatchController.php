<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\Analytics;
use App\Services\MatchEngine;

class MatchController extends Controller
{
    public function match(Request $request, MatchEngine $engine)
    {
        $interests       = $request->input('interests', []);
        $subjects        = $request->input('subjects', []);
        $subjectsDetail  = $request->input('subjects_detail', []);
        $heroes          = $request->input('heroes', []);
        $heroNote        = $request->input('hero_note');
        $prefecture      = $request->input('prefecture');

        \App\Support\Analytics::track(
            'interest_submit',
            [
                'interest_count' => count($interests),
                'subject_count'  => count($subjects) + count($subjectsDetail),
                'hero_count'     => count($heroes),
            ],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );

        $payload = [
            'interests'       => $interests,
            'subjects'        => $subjects,
            'subjects_detail' => $subjectsDetail,
            'heroes'          => $heroes,
            'hero_note'       => $heroNote,
            'prefecture'      => $prefecture,
        ];

        $candidates = $engine->buildCandidates($payload);

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
                'top_interest'    => $interests[0] ?? null,
            ],
            auth()->id(),
            auth()->check() ? auth()->user()->role ?? null : null
        );

        // HTMLで返す
        return view('match-results', compact('candidates'));
    }
}
