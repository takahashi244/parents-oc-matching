# starter_pack/ 配置案内（Laravel プロジェクトにドロップ）

このフォルダの中身を **Laravel Sail プロジェクト直下**に配置してください（既存ファイルがあればマージ）。

```
/your-laravel-project
├─ app
│  ├─ Http/Controllers
│  │  ├─ MatchController.php
│  │  └─ OCEventController.php
│  ├─ Models
│  │  ├─ Department.php
│  │  ├─ School.php
│  │  └─ OCEvent.php
│  └─ Support/Analytics.php
├─ config/features.php
├─ database
│  ├─ migrations/  （*.php：4ファイル）
│  └─ seeders
│     ├─ InitSeeder.php
│     └─ data/
│        ├─ schools.csv
│        ├─ departments.csv
│        └─ oc_events.csv
├─ resources
│  └─ views
│     ├─ interest-form.blade.php
│     ├─ match-results.blade.php
│     ├─ components/match-card.blade.php
│     └─ oc/detail.blade.php
└─ routes/web.php   （このファイルの内容を既存に追記）
```

## 次のアクション
1. 配置 → `./vendor/bin/sail up -d`
2. 認証導入 → `./vendor/bin/sail composer require laravel/breeze --dev && ./vendor/bin/sail artisan breeze:install blade && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev`
3. DB → `./vendor/bin/sail artisan migrate && ./vendor/bin/sail artisan db:seed --class=InitSeeder`
4. ブラウザ → `http://localhost` で診断→マッチ→OC詳細（予約は外部）を確認
