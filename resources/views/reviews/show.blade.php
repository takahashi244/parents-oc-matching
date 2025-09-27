<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  @vite('resources/css/app.css')
  <title>レビュー詳細</title>
</head>
<body class="bg-gray-50">
<main class="max-w-3xl mx-auto p-6 space-y-6">
  @if(session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 rounded text-sm">{{ session('ok') }}</div>
  @endif
  <header class="space-y-2">
    <a href="{{ route('reviews.index') }}" class="text-sm text-gray-500">← 公開レビューに戻る</a>
    <h1 class="text-2xl font-bold">{{ $review->school_name }} {{ $review->dept_name }}</h1>
    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
      <span>{{ $review->created_at }}</span>
      <span>投稿者：{{ $review->author_role === 'parent' ? '保護者' : '子ども' }}</span>
      <span class="text-amber-500">評価：{{ $review->rating ?? '－' }}</span>
      <a href="{{ route('schools.show', ['id' => $review->school_id ?? '']) }}" class="text-blue-600">学校ページを見る</a>
    </div>
  </header>

  <section class="bg-white border rounded-2xl p-4 space-y-4">
    <div>
      <h2 class="text-lg font-semibold">良かった点</h2>
      <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
        @forelse($pros as $item)
          <li>{{ $item }}</li>
        @empty
          <li class="text-gray-400">（特になし）</li>
        @endforelse
      </ul>
    </div>

    <div>
      <h2 class="text-lg font-semibold">気になった点</h2>
      <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
        @forelse($cons as $item)
          <li>{{ $item }}</li>
        @empty
          <li class="text-gray-400">（特になし）</li>
        @endforelse
      </ul>
    </div>

    @if($review->notes)
      <div>
        <h2 class="text-lg font-semibold">メモ</h2>
        <div class="bg-gray-50 border rounded p-3 text-sm whitespace-pre-wrap">{{ $review->notes }}</div>
      </div>
    @endif

    <div>
      <a href="{{ route('oc.show', ['id' => $review->ocev_id]) }}" class="px-4 py-2 rounded bg-black text-white text-sm">このOCの詳細を見る</a>
    </div>
  </section>
</main>
</body>
</html>
