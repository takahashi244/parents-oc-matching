---
doc_type: checklist
title: Definition of Ready（DoR） v1.0 — Sprint1/2（MVP）
version: 1.0
owner: Taka244（PM）
updated: 2025-09-26
---

# 0. 目的
Sprint 1/2 に着手するための「**実装開始OK条件**」を明文化。DoRを満たした Backlog だけ着手。

# 1. 全体共通（環境・方針）
- [ ] ADR-001（Laravel 11 + Sail + Blade + Tailwind + **MySQL 8.0**）がリポジトリに格納済み（/docs）。  
- [ ] ERD v1.0 の Migration が `php artisan migrate` で通る（ローカル）。  
- [ ] Seed データ（schools/departments/oc_events）が投入済み（少量でも可）。  
- [ ] Feature Flag：`search_enabled=false`（検索は**非表示**）。  
- [ ] 計測の送信関数 `trackEvent(name, props)` がアプリから呼べる。  
- [ ] マイクロコピー v1.0 をUIに反映（**「合わなければ他の候補も見てみよう。」** を含む）。  
- [ ] 安全運用：モデレーション（α：**目視**）のプレイブックが共有済み。

# 2. FR別 DoR

## FR-001 興味ベース診断
- [ ] タグ初期セット（14）と slug が確定（仕様 v0.2）。  
- [ ] 好き科目＝**基本8（国/数/理/社/英/情/芸/保体）**、社会/理科の細分選択が ON。  
- [ ] 入力3分以内のUX（任意でタイマー計測）。

## FR-002 簡易マッチングv0
- [ ] 上位5件＋**理由チップ≥3**を返す。  
- [ ] 文型A/B/Cで理由を生成（テンプレ固定）。  
- [ ] 多様性ルール（同一学校の連番回避）を実装。  
- [ ] 欠損（レビューなし/OC不明）でも候補は返す。

## FR-003 候補共有（リンク/QR）
- [ ] ログインなしで閲覧可。  
- [ ] **有効期限=7日**（トークン＆期限表示）。  
- [ ] 共有完了トースト表示。  
- [ ] `share_open` が計測される。

## FR-004 OC候補詳細→予約（外部）
- [ ] `oc_detail_view` / `reserve_click` を計測。  
- [ ] 検索UIは**非表示**。  
- [ ] 予約は外部URLに遷移。

## FR-005 内見メモ（親/子）
- [ ] 親/子とも各**10項目**を1〜5で評価できる。  
- [ ] 親用画面で総合評価と良かった点/気になった点を入力し、確認画面を経てレビュー公開につながる。

## FR-006 レビュー（確認画面経由）
- [ ] 公開は**注意事項への同意チェック必須**。  
- [ ] 親用メモ送信後に確認画面で内容を表示し、公開ボタンで初めて公開される。初期は**目視運用**（通報対応可）。

## FR-007 認証/同意（MVP最小）
- [ ] Breeze初期設定でOK。未成年の同意文言を表示。

## FR-008 計測
- [ ] 主要イベントが送れる：`interest_submit` `match_view` `share_open` `oc_detail_view` `reserve_click` `oc_memo_create` `review_publish`  
- [ ] PIIはイベントに**含めない**（propsは数値/タグのみ）。

# 3. 非機能（抜粋）
- [ ] p95 応答 < 500ms（暫定）  
- [ ] 500系/JSエラーはログ出力（コンソールで可）

# 4. Exit Criteria（Sprint）
- [ ] Sprint Demo 動画（2分）を撮り、PRDのKPIに沿って**α判定**の見込みが説明できる。
