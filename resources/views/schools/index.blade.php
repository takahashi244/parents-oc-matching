<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite('resources/css/app.css')
  <title>学校別レビュー一覧</title>
</head>
<body class="bg-gray-50">
<main class="max-w-5xl mx-auto p-6 space-y-6">
  <header class="space-y-2">
    <h1 class="text-2xl font-bold">学校別レビュー一覧</h1>
    <p class="text-sm text-gray-600">エリアごとの学校と平均評価・レビュー件数を掲載しています。学校名をクリックすると詳細が表示されます。</p>
  </header>

  @forelse($groupedSchools as $prefecture => $schools)
    <section class="space-y-4">
      <h2 class="text-xl font-semibold">{{ $prefecture ?: 'エリア未設定' }}</h2>
      <div class="grid md:grid-cols-2 gap-4">
        @foreach($schools as $school)
          <article class="bg-white border rounded-2xl p-4 space-y-2">
            <h3 class="text-lg font-semibold">
              <a href="{{ route('schools.show', ['id' => $school->school_id]) }}" class="text-blue-600 hover:underline">{{ $school->school_name }}</a>
            </h3>
            <div class="text-sm text-gray-600">種別：{{ $school->school_type === 'vocational' ? '専門学校' : '大学・短大' }}</div>
            <div class="flex items-center gap-3 text-sm text-gray-700">
              <span>レビュー件数：{{ $school->review_count }}</span>
              <span>平均評価：{{ $school->avg_rating ?? '－' }}</span>
            </div>
          </article>
        @endforeach
      </div>
    </section>
  @empty
    <p class="text-sm text-gray-500">まだレビュー対象の学校がありません。</p>
  @endforelse
</main>
</body>
</html>
