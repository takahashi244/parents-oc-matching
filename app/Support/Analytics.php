<?php
namespace App\Support;

use Illuminate\Support\Facades\DB;

class Analytics
{
    public static function track(string $event, array $props = [], ?int $userId = null, ?string $role = null): void
    {
        try {
            DB::table('event_logs')->insert([
                'event'      => $event,
                'props'      => json_encode($props, JSON_UNESCAPED_UNICODE),
                'user_id'    => $userId,
                'role'       => $role,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // MVP: 計測失敗は無視（UXを止めない）
        }
    }
}
