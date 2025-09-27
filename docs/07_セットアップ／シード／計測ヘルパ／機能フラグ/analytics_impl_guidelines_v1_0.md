---
doc_type: impl_guideline
title: analytics_impl_guidelines_v1_0.md — イベント計測の実装
version: 1.0
owner: Taka244（PM）
updated: 2025-09-16
---

# 0. 方針（MVP）
- 送信先：DBの `event_logs` テーブル（ベータでベンダーに切替）
- 命名：イベントは `snake_case`、**PIIは送らない**（タグ/数値のみ）

# 1. ヘルパ関数
`app/Support/Analytics.php`
```php
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
            // MVPでは失敗しても何もしない（重複計上・遅延を避ける）
        }
    }
}
```

# 2. 呼び出し例（Controller）
```php
use App\Support\Analytics;

// 興味入力 完了
Analytics::track('interest_submit', [
  'tag_count' => count($tags),
  'time_spent_ms' => $request->integer('time_spent_ms', 0),
  'tag_list' => $tags,
  'entry_role' => $request->input('entry_role', 'parent'),
], optional(auth()->user())->id, session('role'));

// マッチ表示
Analytics::track('match_view', [
  'candidate_count' => count($candidates),
  'top_tags' => array_slice($tags, 0, 3),
]);

// OC詳細
Analytics::track('oc_detail_view', [
  'ocev_id' => $ocevId,
  'dept_id' => $deptId,
  'school_id' => $schoolId,
  'from' => $from, // 'match' or 'share'
]);

// 予約クリック
Analytics::track('reserve_click', [
  'ocev_id' => $ocevId,
  'dest_domain' => parse_url($url, PHP_URL_HOST),
]);

// 内見メモ開始/保存
Analytics::track('oc_memo_create', [
  'ocev_id' => $ocevId,
  'actor_role' => $role,
  'filled_ratio' => $filledRatio,
]);

// レビュー公開
Analytics::track('review_publish', [
  'ocev_id' => $ocevId,
  'word_count' => mb_strlen($text),
  'moderation' => 'manual',
  'status' => 'published',
]);
```

# 3. Blade/JSから投げたい場合（任意）
非同期で `/api/track` を作るより、MVPでは**サーバー側で確定時のみ**計上が簡単。

# 4. 検証
- DBで `select event, count(*) from event_logs group by 1;`
- PIIが props に入っていないこと（メール/名前など禁止）
