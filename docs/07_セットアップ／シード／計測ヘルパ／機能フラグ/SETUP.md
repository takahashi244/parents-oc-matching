---
doc_type: setup
title: SETUP.md — Laravel Sail + MySQL + Blade/Tailwind（MVP環境）
version: 1.0
owner: Taka244（PM）
updated: 2025-09-16
---

# 0. 前提ツール
- Docker Desktop（最新版）
- Git / Node.js LTS / npm
- （任意）Composer

# 1. プロジェクト作成（Sail推奨）
**方法A：公式スクリプト（Composer不要）**
```bash
curl -s https://laravel.build/oc-app | bash
cd oc-app
./vendor/bin/sail up -d
```

**方法B：Composerから**
```bash
composer create-project laravel/laravel oc-app
cd oc-app
php artisan sail:install   # Database: MySQL を選択
./vendor/bin/sail up -d
```

# 2. .env 設定（MySQL 8.0）
`.env`（生成済み）を確認：
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=oc_app
DB_USERNAME=sail
DB_PASSWORD=password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_0900_ai_ci
```

# 3. 認証 + UI（Breeze + Blade）
```bash
./vendor/bin/sail composer require laravel/breeze --dev
./vendor/bin/sail artisan breeze:install blade
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev   # 開発（本番は build）
```

# 4. DBマイグレーション
ERD v1.0 の定義通りに Migration を作成→実行：
```bash
./vendor/bin/sail artisan migrate
```

# 5. フロント（Tailwind 確認）
`resources/css/app.css` に Tailwind の `@tailwind` 記述があること。  
`npm run dev` 実行中に、クラスが反映されることを確認。

# 6. 機能フラグの設定（先に用意）
`config/features.php` を作成（別紙「feature_flags_v0_1.md」参照）。  
`.env` に既定値：
```
SEARCH_ENABLED=false
CAREER_STORIES_ENABLED=false
AUTO_MODERATION_ENABLED=false
TEACHER_VIEW_ENABLED=false
```

# 7. 計測の雛形（先に用意）
`app/Support/Analytics.php` に `trackEvent($name, array $props = [])` を用意（別紙「analytics_impl_guidelines_v1_0.md」参照）。

# 8. Seed（最小データ投入）
CSV 3種（schools/departments/oc_events）を `database/seeders/data/` に置き、  
`php artisan db:seed --class=InitSeeder` を実行（別紙「seed_plan_v1_0.md」参照）。

# 9. 動作確認（ブラウザ）
- `http://localhost` にアクセス  
- トップの**診断フォーム**→**マッチ結果**が表示される（ダミーデータでOK）

# 10. よくある詰まり
- **Sail が起動しない**：ポート競合（3306/80/443）→他アプリ停止  
- **CSSが当たらない**：`npm run dev` を再実行、Viteの警告確認  
- **DB接続エラー**：`.env` の DB を再確認→`sail artisan config:clear`
