<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite('resources/css/app.css')
  <title>{{ $school->school_name }}のレビュー</title>
</head>
<body class="bg-gray-50">
<main class="max-w-4xl mx-auto p-6 space-y-6">
  <header class="space-y-2">
    <a href="{{ route('schools.index') }}" class="text-sm text-gray-500">← 学校一覧に戻る</a>
    <h1 class="text-2xl font-bold">{{ $school->school_name }}</h1>
    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
      <span>エリア：{{ $school->prefecture }}</span>
      <span>種別：{{ $school->school_type === 'vocational' ? '専門学校' : '大学・短大' }}</span>
      <span>レビュー件数：{{ $reviewCount }}</span>
      <span>平均評価：{{ $avgRating ?? '－' }}</span>
    </div>
  </header>

  <section class="space-y-4">
    @forelse($reviews as $it)
      <article class="bg-white border rounded-2xl p-4 space-y-3">
        <div class="text-sm text-gray-500">
          {{ $it->created_at }}・{{ $it->author_role === 'parent' ? '保護者' : '子ども' }}
        </div>
        <h2 class="text-lg font-semibold">{{ $it->dept_name }}</h2>
        <div class="text-sm text-gray-600">OC: {{ $it->date }}{{ $it->place ? '（'.$it->place.'）' : '' }}</div>
        <div class="text-amber-500" aria-label="rating">
          @for ($i = 1; $i <= 5; $i++)
            <span>{{ $i <= (int)($it->rating ?? 0) ? '★' : '☆' }}</span>
          @endfor
        </div>
        @php
          $pros = array_values(array_filter(json_decode($it->pros ?? '[]', true) ?? []));
          $cons = array_values(array_filter(json_decode($it->cons ?? '[]', true) ?? []));
        @endphp
        @if(!empty($pros))
          <div class="text-sm text-gray-700"><span class="font-semibold">良かった点：</span>{{ implode(' / ', $pros) }}</div>
        @endif
        @if(!empty($cons))
          <div class="text-sm text-gray-700"><span class="font-semibold">気になった点：</span>{{ implode(' / ', $cons) }}</div>
        @endif
        <div>
          <a href="{{ route('reviews.show', ['rev_id' => $it->rev_id]) }}" class="text-sm text-blue-600">詳細を見る →</a>
        </div>
      </article>
    @empty
      <p class="text-sm text-gray-500">この学校の公開レビューはまだありません。</p>
    @endforelse
  </section>

  <div>
    {{ $reviews->links() }}
  </div>
</main>
</body>
</html>
