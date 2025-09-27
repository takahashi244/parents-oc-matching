---
doc_type: feature_flags
title: feature_flags_v0_1.md — 機能フラグの設計と使い方
version: 0.1
owner: Taka244（PM）
updated: 2025-09-16
---

# 0. 目的
MVPでは**検索を非表示**にし、段階解禁（地域・時期）に備えて**機能フラグ**で切り替える。

# 1. .env の既定値
```
SEARCH_ENABLED=false
CAREER_STORIES_ENABLED=false
AUTO_MODERATION_ENABLED=false
TEACHER_VIEW_ENABLED=false
```

# 2. config ファイル
`config/features.php`
```php
<?php
return [
  'search_enabled'         => env('SEARCH_ENABLED', false),
  'career_stories_enabled' => env('CAREER_STORIES_ENABLED', false),
  'auto_moderation_enabled'=> env('AUTO_MODERATION_ENABLED', false),
  'teacher_view_enabled'   => env('TEACHER_VIEW_ENABLED', false),
];
```

# 3. Blade での使い方
```php
{{-- 検索UI（初期は非表示） --}}
@if(config('features.search_enabled'))
  @include('partials.search')
@endif
```

# 4. コントローラでの分岐
```php
if (!config('features.search_enabled')) {
    abort(404); // ルート自体をOFFにする場合
}
```

# 5. 将来（GA）に向けて
- 地域別・ユーザー属性別のフラグは GrowthBook などの外部ツールへ移行
