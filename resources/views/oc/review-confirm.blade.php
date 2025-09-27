<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite('resources/css/app.css')
  <title>レビュー内容の確認</title>
</head>
<body class="bg-gray-50">
<main class="max-w-3xl mx-auto p-6 space-y-6">
  <header class="space-y-3">
    <a href="{{ route('oc.memo.parent', ['id' => $ev->ocev_id]) }}" class="text-sm text-gray-500">← 入力画面に戻る</a>
    <div>
      <h1 class="text-2xl font-bold">レビュー内容の確認</h1>
      <p class="mt-1 text-sm text-gray-600">公開前に、入力した情報をもう一度確認してください。</p>
    </div>
  </header>

  <section class="bg-white border rounded-2xl p-4 space-y-3">
    <h2 class="text-lg font-semibold">対象のOC</h2>
    <p class="text-sm text-gray-700">{{ $ev->dept_id }}（{{ $ev->date }}／{{ $ev->place }}）</p>
  </section>

  <section class="bg-white border rounded-2xl p-4 space-y-4">
    <div class="flex flex-wrap gap-4 text-sm text-gray-700">
      <div>
        <span class="block text-xs text-gray-500">項目の平均評価</span>
        <div class="flex items-center gap-2 text-base text-amber-500">
          @php $avg = $draft['average_rating'] ?? null; @endphp
          @for($i = 1; $i <= 5; $i++)
            <span>{{ $avg !== null && $i <= floor($avg) ? '★' : '☆' }}</span>
          @endfor
          <span class="text-sm text-gray-700">{{ $avg !== null ? number_format($avg, 1) : '－' }}</span>
        </div>
      </div>
      <div>
        <span class="block text-xs text-gray-500">公開する総合評価</span>
        <div class="flex items-center gap-2 text-base text-amber-500">
          @php $overall = $draft['overall_rating'] ?? null; @endphp
          @for($i = 1; $i <= 5; $i++)
            <span>{{ $overall !== null && $i <= (int) $overall ? '★' : '☆' }}</span>
          @endfor
          <span class="text-sm text-gray-700">{{ $overall ?? '－' }}</span>
        </div>
      </div>
    </div>

    <div>
      <h3 class="text-sm font-semibold text-gray-700">各チェック項目</h3>
      <ul class="mt-2 space-y-2 text-sm text-gray-700">
        @foreach($scoredItems as $item)
          <li class="flex items-center justify-between border border-gray-100 rounded-lg px-3 py-2">
            <span>{{ $item['label'] }}</span>
            <span class="text-amber-500 font-semibold">{{ $item['score'] }}</span>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <h3 class="text-sm font-semibold text-gray-700">良かった点</h3>
        <p class="mt-2 whitespace-pre-wrap text-sm text-gray-800">{{ $draft['good_text'] ?: '（入力なし）' }}</p>
      </div>
      <div>
        <h3 class="text-sm font-semibold text-gray-700">気になった点</h3>
        <p class="mt-2 whitespace-pre-wrap text-sm text-gray-800">{{ $draft['concern_text'] ?: '（入力なし）' }}</p>
      </div>
    </div>
  </section>

  <section class="bg-white border rounded-2xl p-4 space-y-4">
    <p class="text-sm text-gray-600">公開後でも、学校ページから同じOCのレビューを再投稿して内容を更新できます。</p>
    <div class="flex flex-col md:flex-row gap-3">
      <a href="{{ route('oc.memo.parent', ['id' => $ev->ocev_id]) }}" class="inline-flex justify-center items-center px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">内容を修正する</a>
      <form method="POST" action="{{ route('oc.review.publish', ['id' => $ev->ocev_id]) }}" class="flex-1">
        @csrf
        <button class="w-full px-4 py-3 rounded-lg bg-black text-white text-sm font-semibold">この内容で公開する</button>
      </form>
    </div>
  </section>
</main>
</body>
</html>
