<?php

namespace App\Http\Controllers;

use App\Support\Analytics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function store(Request $request)
    {
        $candidates = session('match.last_candidates', []);

        if (empty($candidates)) {
            return back()->with('error', '共有できる診断結果がありません。診断を実施してから共有してください。');
        }

        $token = Str::lower(Str::random(40));

        DB::table('share_links')->where('expires_at', '<', Carbon::now())->delete();

        DB::table('share_links')->insert([
            'token'      => $token,
            'user_id'    => auth()->id(),
            'payload'    => json_encode($candidates, JSON_UNESCAPED_UNICODE),
            'expires_at' => Carbon::now()->addDays(7),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('share.show', ['token' => $token])
            ->with('ok', '共有用リンクを作成しました（有効期限：7日）。');
    }

    public function show(string $token)
    {
        $link = DB::table('share_links')->where('token', $token)->first();

        if (!$link) {
            return response()->view('share.expired', ['message' => 'この共有リンクは存在しません。'], 404);
        }

        $expiresAt = Carbon::parse($link->expires_at);

        if (Carbon::now()->greaterThan($expiresAt)) {
            return response()->view('share.expired', ['message' => 'この共有リンクは有効期限切れです。'], 410);
        }

        $candidates = json_decode($link->payload, true) ?? [];

        Analytics::track('share_open', [
            'token'    => $token,
            'count'    => count($candidates),
            'expired'  => false,
        ]);

        return view('share.show', [
            'candidates' => $candidates,
            'expires_at' => $expiresAt,
            'token'      => $token,
        ]);
    }
}
