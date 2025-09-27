<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = DB::table('schools as s')
            ->leftJoin('departments as d', 'd.school_id', '=', 's.school_id')
            ->leftJoin('oc_events as e', 'e.dept_id', '=', 'd.dept_id')
            ->leftJoin('reviews as r', function ($join) {
                $join->on('r.ocev_id', '=', 'e.ocev_id')
                    ->where('r.is_published', true);
            })
            ->select([
                's.school_id',
                's.school_name',
                's.prefecture',
                's.school_type',
                DB::raw('COUNT(r.rev_id) as review_count'),
                DB::raw('AVG(r.rating) as avg_rating'),
            ])
            ->groupBy('s.school_id', 's.school_name', 's.prefecture', 's.school_type')
            ->orderBy('s.prefecture')
            ->orderBy('s.school_name')
            ->get()
            ->map(function ($school) {
                $school->avg_rating = $school->avg_rating ? round($school->avg_rating, 1) : null;
                return $school;
            })
            ->groupBy('prefecture');

        return view('schools.index', [
            'groupedSchools' => $schools,
        ]);
    }

    public function show(string $schoolId)
    {
        $school = DB::table('schools')->where('school_id', $schoolId)->first();

        if (!$school) {
            abort(404);
        }

        $stats = DB::table('schools as s')
            ->leftJoin('departments as d', 'd.school_id', '=', 's.school_id')
            ->leftJoin('oc_events as e', 'e.dept_id', '=', 'd.dept_id')
            ->leftJoin('reviews as r', function ($join) {
                $join->on('r.ocev_id', '=', 'e.ocev_id')
                    ->where('r.is_published', true);
            })
            ->select([
                DB::raw('COUNT(r.rev_id) as review_count'),
                DB::raw('AVG(r.rating) as avg_rating'),
            ])
            ->where('s.school_id', $schoolId)
            ->first();

        $reviews = DB::table('reviews as r')
            ->join('oc_events as e', 'e.ocev_id', '=', 'r.ocev_id')
            ->join('departments as d', 'd.dept_id', '=', 'e.dept_id')
            ->where('d.school_id', $schoolId)
            ->where('r.is_published', true)
            ->orderByDesc('r.created_at')
            ->select([
                'r.rev_id',
                'r.ocev_id',
                'r.author_role',
                'r.rating',
                'r.pros',
                'r.cons',
                'r.notes',
                'r.created_at',
                'e.date',
                'e.place',
                'd.dept_name',
            ])
            ->paginate(10)
            ->withQueryString();

        $avgRating = $stats && $stats->avg_rating ? round($stats->avg_rating, 1) : null;
        $reviewCount = $stats ? (int) $stats->review_count : 0;

        return view('schools.show', [
            'school'      => $school,
            'avgRating'   => $avgRating,
            'reviewCount' => $reviewCount,
            'reviews'     => $reviews,
        ]);
    }
}
