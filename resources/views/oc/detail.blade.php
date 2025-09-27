<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite('resources/css/app.css')
  <title>OC候補詳細</title>
</head>
<body class="bg-gray-50">
@php
    $parentRatings = $parentRatings ?? [];
    $childRatings = $childRatings ?? [];
    $parentLabels  = $parentLabels ?? [];
    $childLabels   = $childLabels ?? [];
    $parentMemoRecordedAt = $parentMemoRecordedAt ?? null;
    $childMemoRecordedAt  = $childMemoRecordedAt ?? null;
@endphp

<main class="max-w-4xl mx-auto p-6 space-y-6">
  <section class="bg-white border rounded-2xl p-6 space-y-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div class="space-y-1">
        @if($schoolName)
          <h1 class="text-2xl md:text-3xl font-bold">{{ $schoolName }}</h1>
        @endif
        <h2 class="text-2xl font-semibold">{{ $deptName ?? $ev->dept_id }} のOC</h2>
      </div>
      @if($ev->reservation_url)
        <a href="{{ route('oc.reserve', ['id' => $ev->ocev_id]) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-black text-white text-sm font-semibold shadow-sm hover:bg-gray-900">予約へ（外部サイト）</a>
      @endif
    </div>

    <div class="flex flex-wrap gap-3 text-sm text-gray-700">
      <span>日付：{{ $ev->date }}</span>
      @if($ev->place)
        <span>会場：{{ $ev->place }}</span>
      @endif
    </div>

    <p class="text-sm text-gray-500">検索機能は準備中です。まずは診断の候補から体験しましょう。</p>

    @if(!empty($reasons))
      <div class="flex flex-wrap gap-2 pt-2">
        @foreach($reasons as $reason)
          <span class="px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-sm text-amber-700">{{ $reason }}</span>
        @endforeach
      </div>
    @endif

    @if (session('ok'))
      <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
      <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
        <ul class="space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </section>

  <section class="grid gap-6 md:grid-cols-2">
    <div class="bg-white border rounded-2xl p-4 space-y-3">
      <h2 class="text-lg font-semibold">親用レビュー入力</h2>
      <p class="text-sm text-gray-600">ここで記入するとレビューとして公開されます。</p>
      @if(!empty($parentRatings))
        <div class="rounded-xl bg-amber-50/60 border border-amber-200 p-3 space-y-2 text-sm text-amber-800">
          <p class="text-xs text-amber-700">前回（{{ $parentMemoRecordedAt ?? '記録日不明' }}）にあなたが保存したメモ</p>
          <ul class="list-disc list-inside space-y-1">
            @foreach($parentRatings as $key => $value)
              <li>{{ $parentLabels[$key] ?? $key }}：{{ $value }}</li>
            @endforeach
          </ul>
          <p class="text-xs text-amber-700">新しく投稿すると最新のレビューとして公開されます。</p>
        </div>
      @else
        <p class="text-sm text-gray-400">まだ記入がありません。</p>
      @endif
      <a href="{{ route('oc.memo.parent', ['id' => $ev->ocev_id]) }}" class="inline-block px-4 py-2 rounded bg-black text-white text-sm">親用レビューを記入する</a>
    </div>

    <div class="bg-white border rounded-2xl p-4 space-y-3">
      <h2 class="text-lg font-semibold">子ども用メモ</h2>
      <p class="text-sm text-gray-600">子ども自身の感じ方を1〜5で残しておくと、あとで話がしやすくなります。</p>
      @if(!empty($childRatings))
        <div class="rounded-xl bg-sky-50 border border-sky-200 p-3 space-y-2 text-sm text-sky-800">
          <p class="text-xs text-sky-700">前回（{{ $childMemoRecordedAt ?? '記録日不明' }}）に子どもが残したメモ</p>
          <ul class="list-disc list-inside space-y-1">
            @foreach($childRatings as $key => $value)
              <li>{{ $childLabels[$key] ?? $key }}：{{ $value }}</li>
            @endforeach
          </ul>
          <p class="text-xs text-sky-700">再度メモを保存するとこの情報が更新されます。</p>
        </div>
      @else
        <p class="text-sm text-gray-400">まだ記入がありません。</p>
      @endif
      <a href="{{ route('oc.memo.child', ['id' => $ev->ocev_id]) }}" class="inline-block px-4 py-2 rounded bg-black text-white text-sm">子ども用メモを記入する</a>
    </div>
  </section>

  <section class="bg-white border rounded-2xl p-4 space-y-3">
    <h2 class="text-lg font-semibold">レビューについて</h2>
    <p class="text-sm text-gray-600">親用レビューは確認画面を挟んでから公開できます（初期は目視で確認します）。</p>
    <a href="{{ route('reviews.index') }}" class="inline-block px-4 py-2 rounded border text-sm">公開レビューを見る</a>
  </section>
</main>
</body>
</html>
