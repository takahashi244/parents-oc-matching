---
doc_type: seed_plan
title: seed_plan_v1_0.md — 初期データ投入（CSV）
version: 1.0
owner: Taka244（PM）
updated: 2025-09-16
---

# 0. 目的
**schools / departments / oc_events** の最小データを CSV から投入し、マッチ結果〜OC導線までを動かす。

# 1. CSVを配置
`database/seeders/data/` に以下3ファイルを配置（文字コード：UTF-8、カンマ区切り）：
- `schools.csv`
- `departments.csv`
- `oc_events.csv`

# 2. シーダの作成（コピペ可）
`database/seeders/InitSeeder.php`
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class InitSeeder extends Seeder
{
    public function run(): void
    {
        $base = database_path('seeders/data');
        $this->importCsv(DB::table('schools'),     "$base/schools.csv");
        $this->importCsv(DB::table('departments'), "$base/departments.csv");
        $this->importCsv(DB::table('oc_events'),   "$base/oc_events.csv");
    }

    private function importCsv($table, string $path): void
    {
        if (!file_exists($path)) { $this->command->warn("CSV not found: $path"); return; }
        $csv = new SplFileObject($path);
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $header = null;
        foreach ($csv as $row) {
            if ($row === [null] || $row === false) continue;
            if (!$header) { $header = $row; continue; }
            $data = array_combine($header, $row);
            if (!$data) continue;
            // JSON列は配列化
            foreach (['tags'] as $jsonCol) {
                if (isset($data[$jsonCol]) && $data[$jsonCol] !== '') {
                    // "a;b" 形式を ["a","b"] に
                    if (str_contains($data[$jsonCol], ';')) {
                        $data[$jsonCol] = json_encode(explode(';', $data[$jsonCol]), JSON_UNESCAPED_UNICODE);
                    } elseif (str_starts_with(trim($data[$jsonCol]), '[')) {
                        // すでにJSON配列ならそのまま
                    } else {
                        $data[$jsonCol] = json_encode([$data[$jsonCol]], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $data[$jsonCol] = json_encode([]);
                }
            }
            $table->insert($data);
        }
    }
}
```

# 3. 実行手順
```bash
php artisan db:seed --class=InitSeeder
```

# 4. CSVフォーマット（例）
schools.csv
```
school_id,school_name,school_type,prefecture
SCH001,東都大学,university,東京都
SCH002,関西メディア芸術大学,university,大阪府
SCH003,東京コンテンツ専門学校,vocational,東京都
SCH004,北海道国際学院,university,北海道
SCH005,日本看護医療専門学校,vocational,神奈川県
```

departments.csv
```
dept_id,school_id,dept_name,tags
DEP001,SCH001,工学部 情報工学科,"["programming","math_data"]"
DEP002,SCH001,文学部 英語文化学科,"["english_global"]"
DEP003,SCH002,メディア学部 映像デザイン学科,"["media","design_art"]"
DEP004,SCH003,ゲームCG学科,"["game_cg","programming"]"
DEP005,SCH003,サウンドクリエイト学科,"["music","media"]"
DEP006,SCH004,国際学部,"["english_global","history_social"]"
DEP007,SCH005,看護学科,"["medical","science"]"
```

oc_events.csv
```
ocev_id,dept_id,date,start_time,end_time,place,reservation_url
EV001,DEP003,2025-10-12,13:00,15:00,大阪府大阪市,https://example.com/reserve/EV001
EV002,DEP004,2025-10-19,10:00,12:00,東京都渋谷区,https://example.com/reserve/EV002
EV003,DEP005,2025-11-03,10:00,12:00,東京都新宿区,https://example.com/reserve/EV003
EV004,DEP001,2025-10-26,14:00,16:00,東京都文京区,https://example.com/reserve/EV004
EV005,DEP002,2025-10-05,13:30,15:00,東京都世田谷区,https://example.com/reserve/EV005
EV006,DEP006,2025-11-09,11:00,13:00,北海道札幌市,https://example.com/reserve/EV006
EV007,DEP007,2025-10-13,10:00,12:00,神奈川県横浜市,https://example.com/reserve/EV007
```

# 5. 確認
- `select count(*) from schools;` などで件数を確認  
- マッチ結果画面に、メディア/サウンド/ゲーム系の候補が表示される
