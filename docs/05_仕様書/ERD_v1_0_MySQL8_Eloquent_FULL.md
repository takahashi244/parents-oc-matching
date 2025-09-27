---
doc_type: erd
title: ERD v1.0（MySQL 8 / Eloquent 前提・統合版）
version: 1.0
owner: Taka244（PM）
updated: 2025-09-16
---

# 0. 目的
MVPに必要な**DBスキーマの単一の参照文書（Single Source of Truth）**です。  
MySQL 8.0 / Laravel Eloquent を前提に、テーブル/カラム/制約/インデックス、命名規約、マイグレーション雛形、モデルの `casts`、データ保持/削除ポリシーまでを1枚にまとめました。

---

# 1. 命名規約・共通方針
- 文字コード/照合順序：`utf8mb4` / `utf8mb4_0900_ai_ci`
- テーブル/カラム：`snake_case`、主キーは **単純キー**（BIGINT自動採番 or 文字列ID）
- 時刻：`created_at` / `updated_at`（TIMESTAMP）を原則採用
- JSON：配列/オブジェクトは **MySQL JSON型** を使用し、モデルで `casts` を `array` に設定
- 外部キー：`ON DELETE CASCADE` を基本（事故防止のため参照設計を優先）
- ID体系：
  - ユーザー系：BIGINT自動採番（`users.id` など）
  - 学校・学部・OCなど**外部CSV由来**は**文字列ID**（`SCH001` / `DEP001` / `EV001`）
- 地理：MVPは都道府県・市区町村など**文字列**で保持（距離は後述のアプリ側計算）

---

# 2. テーブル定義

## 2.1 users（ユーザー）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| id | BIGINT PK AI | ✔ | 自動採番 |
| role | ENUM('parent','student') | ✔ | 役割 |
| email | VARCHAR(191) |  | 認証で使用（Breeze想定・任意） |
| consent | TINYINT(1) | ✔(default=0) | 同意 |
| created_at / updated_at | TIMESTAMP | ✔ |  |

**INDEX**：`email`（必要に応じてUNIQUE）

---

## 2.2 interest_profiles（興味プロフィール）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| id | BIGINT PK AI | ✔ |  |
| user_id | BIGINT FK→users(id) | ✔ | 所有者 |
| tags | JSON |  | 興味タグ（slug配列） |
| subjects | JSON |  | 好き科目（基本8＋細分） |
| hero | VARCHAR(255) |  | 尊敬人物（任意） |
| updated_at | TIMESTAMP | ✔ | 更新時刻 |

**INDEX**：`user_id`

---

## 2.3 schools（学校）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| school_id | VARCHAR(16) PK | ✔ | 例：SCH001 |
| school_name | VARCHAR(255) | ✔ |  |
| school_type | ENUM('university','vocational') | ✔ | 大学/短大/専門（専門はvocationalに含む） |
| prefecture | VARCHAR(32) | ✔ |  |

---

## 2.4 departments（学部学科）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| dept_id | VARCHAR(16) PK | ✔ | 例：DEP001 |
| school_id | VARCHAR(16) FK→schools(school_id) | ✔ | 所属学校 |
| dept_name | VARCHAR(255) | ✔ |  |
| tags | JSON |  | 学部学科のタグ（例："media;design_art" を配列に） |
| summary | TEXT |  |  |

**INDEX**：`school_id`

---

## 2.5 oc_events（オープンキャンパス）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| ocev_id | VARCHAR(16) PK | ✔ | 例：EV001 |
| dept_id | VARCHAR(16) FK→departments(dept_id) | ✔ | 紐づく学部学科 |
| date | DATE | ✔ | 開催日 |
| start_time / end_time | TIME |  | 任意 |
| place | VARCHAR(255) |  | 開催地（市区町村） |
| reservation_url | VARCHAR(255) |  | 予約先（外部） |

**INDEX**：`(dept_id, date)`

---

## 2.6 matches（診断結果スナップショット）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| id | BIGINT PK AI | ✔ |  |
| user_id | BIGINT FK→users(id) | ✔ | 診断者 |
| candidates | JSON | ✔ | 上位候補（`[{dept_id, ocev_id, score, reasons[]}, ...]`） |
| created_at | TIMESTAMP | ✔(default NOW) |  |

**INDEX**：`user_id`, `created_at`

---

## 2.7 reviews（レビュー）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| rev_id | VARCHAR(16) PK | ✔ | 例：R001 |
| ocev_id | VARCHAR(16) FK→oc_events(ocev_id) | ✔ | 対象OC |
| author_role | ENUM('parent','student') | ✔ | 投稿者の種別 |
| rating | TINYINT UNSIGNED | ✔ | 1〜5 |
| pros / cons | JSON |  | 自動分類された短文配列 |
| notes | TEXT |  | 自由記述 |
| user_id | BIGINT FK→users(id) |  | 投稿者（任意） |
| is_published | TINYINT(1) | ✔(default=0) | 公開フラグ |
| created_at / updated_at | TIMESTAMP | ✔ |  |

**INDEX**：`(ocev_id, is_published)`

---

## 2.8 share_links（共有リンク）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| token | VARCHAR(64) PK | ✔ | ランダム |
| user_id | BIGINT FK→users(id) | ✔ | 発行者 |
| payload | JSON | ✔ | 候補リスト等 |
| expires_at | DATETIME | ✔ | 有効期限 |
| created_at | TIMESTAMP | ✔ |  |

**INDEX**：`expires_at`

---

## 2.9 event_logs（イベント計測）
| カラム | 型 | 必須 | 説明 |
|---|---|---|---|
| id | BIGINT PK AI | ✔ |  |
| event | VARCHAR(64) | ✔ | イベント名（snake_case） |
| props | JSON |  | 付随属性 |
| user_id | BIGINT |  | 任意 |
| role | VARCHAR(16) |  | parent/student |
| created_at | TIMESTAMP | ✔(default NOW) |  |

**INDEX**：`(event, created_at)`

---

# 3. モデルの casts（例）
```php
protected $casts = [
  'tags' => 'array',
  'subjects' => 'array',
  'pros' => 'array',
  'cons' => 'array',
  'candidates' => 'array',
  'props' => 'array',
];
```

---

# 4. マイグレーション雛形（抜粋・コピペ可）
> それぞれ `database/migrations/` に配置。IDや外部キーは必要に応じて修正。

```php
// users
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->enum('role', ['parent','student']);
    $table->string('email')->nullable()->unique();
    $table->boolean('consent')->default(false);
    $table->timestamps();
});
```

```php
// interest_profiles
Schema::create('interest_profiles', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->json('tags')->nullable();
    $table->json('subjects')->nullable();
    $table->string('hero')->nullable();
    $table->timestamp('updated_at')->useCurrent();
});
```

```php
// schools
Schema::create('schools', function (Blueprint $table) {
    $table->string('school_id', 16)->primary();
    $table->string('school_name');
    $table->enum('school_type', ['university','vocational']);
    $table->string('prefecture', 32);
});
```

```php
// departments
Schema::create('departments', function (Blueprint $table) {
    $table->string('dept_id', 16)->primary();
    $table->string('school_id', 16);
    $table->string('dept_name');
    $table->json('tags')->nullable();
    $table->text('summary')->nullable();
    $table->foreign('school_id')->references('school_id')->on('schools')->cascadeOnDelete();
    $table->index('school_id');
});
```

```php
// oc_events
Schema::create('oc_events', function (Blueprint $table) {
    $table->string('ocev_id', 16)->primary();
    $table->string('dept_id', 16);
    $table->date('date');
    $table->time('start_time')->nullable();
    $table->time('end_time')->nullable();
    $table->string('place')->nullable();
    $table->string('reservation_url')->nullable();
    $table->foreign('dept_id')->references('dept_id')->on('departments')->cascadeOnDelete();
    $table->index(['dept_id', 'date']);
});
```

```php
// matches
Schema::create('matches', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->json('candidates'); // [{dept_id, ocev_id, score, reasons[]}, ...]
    $table->timestamp('created_at')->useCurrent();
    $table->index(['user_id', 'created_at']);
});
```

```php
// reviews
Schema::create('reviews', function (Blueprint $table) {
    $table->string('rev_id', 16)->primary();
    $table->string('ocev_id', 16);
    $table->enum('author_role', ['parent','student']);
    $table->unsignedTinyInteger('rating');
    $table->json('pros')->nullable();
    $table->json('cons')->nullable();
    $table->text('notes')->nullable();
    $table->enum('status', ['draft','published'])->default('draft');
    $table->timestamps();
    $table->foreign('ocev_id')->references('ocev_id')->on('oc_events')->cascadeOnDelete();
    $table->index(['ocev_id','status']);
});
```

```php
// share_links
Schema::create('share_links', function (Blueprint $table) {
    $table->string('token', 64)->primary();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->json('payload');
    $table->dateTime('expires_at')->index();
    $table->timestamp('created_at')->useCurrent();
});
```

```php
// event_logs
Schema::create('event_logs', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('event', 64);
    $table->json('props')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('role', 16)->nullable();
    $table->timestamp('created_at')->useCurrent();
    $table->index(['event','created_at']);
});
```

---

# 5. データ保持・削除（MVP方針）
- **レビュー**：本人からの削除依頼に応じて**論理削除は行わず物理削除**（通報/法務に配慮しログは保持）
- **イベントログ**：生データは**90日**保持、集計は継続保持（暫定）
- **共有リンク**：`expires_at` 超過のリンクは定期ジョブで削除

---

# 6. 将来拡張のメモ
- 検索解禁時：全文検索（MySQL 8.0 FULLTEXT）を `reviews.notes` に付加
- 地図/距離：SPATIAL型/インデックスを検討（MVPでは不要）
- 先生ビュー/学校公式コメント：将来のFK/テーブル追加に備え、`schools` のPK形式は維持

---

# 7. DoD（完了条件）
- すべてのFKにインデックスがある
- JSON列の `casts` がモデルに定義されている
- `php artisan migrate` が通る（ローカル）
