<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite('resources/css/app.css')
  <title>OC候補詳細</title>
</head>
<body class="min-h-screen">
@php
    $parentRatings = $parentRatings ?? [];
    $childRatings = $childRatings ?? [];
    $parentLabels  = $parentLabels ?? [];
    $childLabels   = $childLabels ?? [];
    $parentMemoRecordedAt = $parentMemoRecordedAt ?? null;
    $childMemoRecordedAt  = $childMemoRecordedAt ?? null;
@endphp

<main class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">
  <section class="bg-surface border border-border rounded-2xl p-6 space-y-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
      <div class="space-y-2">
        @if($schoolName)
          <h1 class="text-2xl md:text-3xl font-semibold text-text">{{ $schoolName }}</h1>
        @endif
        <h2 class="text-xl md:text-2xl font-semibold text-text">{{ $deptName ?? $ev->dept_id }} のオープンキャンパス</h2>
        <div class="flex flex-wrap gap-3 text-sm text-muted">
          <span class="inline-flex items-center gap-1"><span class="text-icon">📅</span>{{ $ev->date }}</span>
          @if($ev->place)
            <span class="inline-flex items-center gap-1"><span class="text-icon">📍</span>{{ $ev->place }}</span>
          @endif
        </div>
      </div>
      @if($ev->reservation_url)
        <a href="{{ route('oc.reserve', ['id' => $ev->ocev_id]) }}" class="btn-primary text-sm">公式サイトで予約する</a>
      @endif
    </div>

    @if(!empty($reasons))
      <div class="space-y-2">
        <p class="text-xs font-semibold text-muted">診断からのおすすめポイント</p>
        <ul class="space-y-2 text-sm text-text list-disc list-inside">
          @foreach($reasons as $reason)
            <li>{{ $reason }}</li>
          @endforeach
        </ul>
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
    <div class="bg-surface border border-border rounded-2xl p-4 space-y-4">
      <div class="space-y-1">
        <h2 class="text-lg font-semibold text-text">親用レビューを作成する</h2>
        <p class="text-sm text-muted leading-relaxed">体験後の気付きや家で話したポイントをまとめておくと、公開レビューとして共有できます。公開前に確認画面で最終チェックができます。</p>
      </div>
      @if(!empty($parentRatings))
        <div class="rounded-2xl bg-accent/15 border border-accent/50 p-3 space-y-2 text-sm text-text">
          <p class="text-xs text-text/70">前回（{{ $parentMemoRecordedAt ?? '記録日不明' }}）にあなたが保存したメモ</p>
          <ul class="list-disc list-inside space-y-1">
            @foreach($parentRatings as $key => $value)
              <li>{{ $parentLabels[$key] ?? $key }}：{{ $value }}</li>
            @endforeach
          </ul>
          <p class="text-xs text-text/70">加筆して投稿すると最新のレビューとして公開されます。</p>
        </div>
      @else
        <p class="text-sm text-muted">まだ記入がありません。</p>
      @endif
      <div class="space-y-2 text-xs text-muted">
        <p class="font-semibold text-text">おすすめの書き留め方</p>
        <ul class="list-disc list-inside space-y-1">
          <li>良かった点／気になった点を一言ずつ</li>
          <li>子どもとの会話で出た気付きもメモに残す</li>
        </ul>
      </div>
      <a href="{{ route('oc.memo.parent', ['id' => $ev->ocev_id]) }}" class="btn-secondary text-sm">親用レビューを記入する</a>
    </div>

    <div class="bg-surface border border-border rounded-2xl p-4 space-y-4">
      <div class="space-y-1">
        <h2 class="text-lg font-semibold text-text">子ども用メモ</h2>
        <p class="text-sm text-muted leading-relaxed">評価は1〜5でOK。短いコメントでも構いません。感じたことをその場で残しておくと、あとで親子で振り返るときに役立ちます。</p>
      </div>
      @if(!empty($childRatings))
        <div class="rounded-2xl bg-surfaceMuted border border-border p-3 space-y-2 text-sm text-text">
          <p class="text-xs text-text/70">前回（{{ $childMemoRecordedAt ?? '記録日不明' }}）に子どもが残したメモ</p>
          <ul class="list-disc list-inside space-y-1">
            @foreach($childRatings as $key => $value)
              <li>{{ $childLabels[$key] ?? $key }}：{{ $value }}</li>
            @endforeach
          </ul>
          <p class="text-xs text-text/70">再度メモを保存するとこの情報が更新されます。</p>
        </div>
      @else
        <p class="text-sm text-muted">まだ記入がありません。</p>
      @endif
      <a href="{{ route('oc.memo.child', ['id' => $ev->ocev_id]) }}" class="btn-secondary text-sm">子ども用メモを記入する</a>
    </div>
  </section>

  <section class="bg-surface border border-border rounded-2xl p-4 space-y-3">
    <h2 class="text-lg font-semibold text-text">公開レビューについて</h2>
    <p class="text-sm text-muted leading-relaxed">親用レビューは確認画面を通じて公開され、α版では運営が目視確認を行っています。ほかのご家庭の投稿も参考にできます。</p>
    <a href="{{ route('reviews.index') }}" class="btn-secondary text-sm">公開レビューを一覧で見る</a>
  </section>
</main>
</body>
</html>
