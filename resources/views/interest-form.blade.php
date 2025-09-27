<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  @vite('resources/css/app.css')
  <title>保護者OCマッチング｜トップ</title>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="relative overflow-hidden bg-gradient-to-br from-amber-200 via-white to-white">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1600&q=80')] opacity-10 bg-cover bg-center"></div>
    <div class="relative max-w-5xl mx-auto px-6 py-16 flex flex-col gap-10 md:flex-row md:items-center">
      <div class="md:w-3/5 space-y-5">
        <span class="inline-flex items-center gap-2 rounded-full bg-black text-white px-3 py-1 text-xs font-semibold">Alpha Sprint</span>
        <h1 class="text-3xl md:text-4xl font-bold leading-tight">診断→オープンキャンパス→レビューがつながる保護者向けの新しい進路選び導線</h1>
        <p class="text-sm md:text-base text-gray-700">子どもの“うっすらした興味”から、いま行くべき学校/学部/OC候補を5件ピックアップ。体験した内容はメモからレビューへ転記され、他の保護者にも役立つナレッジになります。</p>
        <div class="flex flex-col sm:flex-row gap-3">
          <a href="{{ route('diagnosis.form') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-black text-white font-semibold shadow-lg shadow-black/10">3分で診断を始める</a>
          <a href="{{ route('reviews.index') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-300 text-sm text-gray-700 bg-white">公開レビューを見る</a>
        </div>
        @php
          $reviewCount = 0;
          try {
              if (DB::getSchemaBuilder()->hasTable('reviews')) {
                  $reviewCount = DB::table('reviews')->where('is_published', true)->count();
              }
          } catch (Throwable $e) {
              $reviewCount = 0;
          }

          $shareCount = 0;
          try {
              if (DB::getSchemaBuilder()->hasTable('share_links')) {
                  $shareCount = DB::table('share_links')->count();
              }
          } catch (Throwable $e) {
              $shareCount = 0;
          }
        @endphp

        <dl class="flex flex-wrap gap-6 text-xs md:text-sm text-gray-600">
          <div>
            <dt class="font-semibold text-gray-800">レビュー蓄積</dt>
            <dd>{{ number_format($reviewCount) }} 件</dd>
          </div>
          <div>
            <dt class="font-semibold text-gray-800">共有リンク</dt>
            <dd>{{ number_format($shareCount) }} 件作成</dd>
          </div>
        </dl>
      </div>
      <div class="md:w-2/5 bg-white/80 backdrop-blur rounded-3xl border border-white/70 shadow-xl p-6 space-y-4">
        <h2 class="text-lg font-semibold">体験フロー（α）</h2>
        <ol class="space-y-3 text-sm text-gray-700">
          <li class="flex items-start gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-white text-sm font-semibold">1</span>
            <span>興味診断（タグ/科目/尊敬人物を入力）</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-white text-sm font-semibold">2</span>
            <span>候補5件＋理由を確認 → OC体験で親子別メモを記録</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-white text-sm font-semibold">3</span>
            <span>確認画面でレビューを公開し、学校別ページに蓄積</span>
          </li>
        </ol>
      </div>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-6 py-12 space-y-16">
    <section class="grid md:grid-cols-3 gap-6" aria-label="プロダクトの強み">
      <article class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm">
        <h3 class="text-lg font-semibold">理由が見える推薦</h3>
        <p class="text-sm text-gray-600">興味タグ・好き科目・尊敬人物から“なぜこの学校なのか”を3つの理由チップで明示。親子の会話が始まる前提で設計しています。</p>
      </article>
      <article class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm">
        <h3 class="text-lg font-semibold">親用・子ども用メモ</h3>
        <p class="text-sm text-gray-600">項目評価だけでなく、良かった点／気になった点を整理。公開前に確認画面で整えるので安心してレビュー共有が可能です。</p>
      </article>
      <article class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm">
        <h3 class="text-lg font-semibold">学校別レビュー検索</h3>
        <p class="text-sm text-gray-600">エリア・学問カテゴリ・学校種別でフィルタ。体験談が集まるほど、次の保護者が探しやすくなる循環を目指します。</p>
      </article>
    </section>

    <section class="grid md:grid-cols-2 gap-6">
      <article class="bg-white border rounded-2xl p-6 space-y-4 shadow-sm">
        <h3 class="text-lg font-semibold">診断からOC体験までのイメージ</h3>
        <ol class="text-sm text-gray-700 space-y-2">
          <li>1. 興味診断で子どもの“好き”を整理</li>
          <li>2. 各候補ページから予約リンクへ（外部サイト）</li>
          <li>3. 親用／子ども用ガイドで当日の内見ポイントを記録</li>
          <li>4. メモ内容を確認し、レビューを公開</li>
        </ol>
      </article>
      <article class="bg-white border rounded-2xl p-6 space-y-3 shadow-sm">
        <h3 class="text-lg font-semibold">よくある質問</h3>
        <div class="text-sm text-gray-700 space-y-3">
          <div>
            <p class="font-semibold">レビューはすぐ公開されますか？</p>
            <p>親用メモ送信後に確認画面で内容をチェックできます。公開後は学校別ページから閲覧されますが、初期はチームで目視確認を行います。</p>
          </div>
          <div>
            <p class="font-semibold">子ども用メモは誰が見られますか？</p>
            <p>子ども用メモはアカウント内で共有され、公開レビューには含まれません。親子の振り返りや今後の比較に活用できます。</p>
          </div>
        </div>
      </article>
    </section>

    <section class="bg-white border rounded-2xl p-6 shadow-sm">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold">診断を開始する準備はできましたか？</h2>
          <p class="text-sm text-gray-600">保護者アカウントでログインすると、診断結果やメモ・レビューが保存されます。</p>
        </div>
        <a href="{{ route('diagnosis.form') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-black text-white font-semibold shadow-lg shadow-black/10">診断ページへ進む</a>
      </div>
    </section>
  </main>

  <footer class="bg-black text-white">
    <div class="max-w-5xl mx-auto px-6 py-10 flex flex-col md:flex-row gap-6 md:items-center md:justify-between">
      <div>
        <p class="font-semibold">保護者OCマッチング（仮）</p>
        <p class="text-xs text-gray-300">診断→OC→レビューの循環で、意思決定に寄り添うα版を運営中です。</p>
      </div>
      <div class="flex gap-4 text-xs text-gray-300">
        <a href="{{ route('reviews.index') }}" class="hover:text-amber-200">公開レビュー</a>
        <a href="{{ route('schools.index') }}" class="hover:text-amber-200">学校別ページ</a>
        <a href="{{ route('diagnosis.form') }}" class="hover:text-amber-200">診断を始める</a>
      </div>
    </div>
  </footer>
</body>
</html>
