<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
@vite('resources/css/app.css')
<title>学校別レビュー検索</title>
</head>
<body class="bg-gray-50">
<main class="max-w-5xl mx-auto p-6 space-y-6">
  <header class="space-y-2">
    <h1 class="text-2xl font-bold">学校別レビュー検索</h1>
    <p class="text-sm text-gray-600">エリアや学問カテゴリで絞り込み、気になる学校の体験談をチェックできます。</p>
  </header>

  <section class="bg-white border rounded-2xl p-4">
    <form method="GET" class="grid gap-4 md:grid-cols-5">
      <div class="col-span-2">
        <label for="prefecture" class="block text-xs text-gray-500">エリア</label>
        <select id="prefecture" name="prefecture" class="mt-1 w-full border rounded px-3 py-2 text-sm">
          <option value="">すべて</option>
          @foreach($prefectureOptions as $prefecture)
            <option value="{{ $prefecture }}" {{ ($filters['prefecture'] ?? null) === $prefecture ? 'selected' : '' }}>{{ $prefecture }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-span-2">
        <label for="tag" class="block text-xs text-gray-500">学問カテゴリ</label>
        <select id="tag" name="tag" class="mt-1 w-full border rounded px-3 py-2 text-sm">
          <option value="">すべて</option>
          @foreach($tagOptions as $tag)
            <option value="{{ $tag['code'] }}" {{ ($filters['tag'] ?? null) === $tag['code'] ? 'selected' : '' }}>{{ $tag['label'] }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="school_type" class="block text-xs text-gray-500">種別</label>
        <select id="school_type" name="school_type" class="mt-1 w-full border rounded px-3 py-2 text-sm">
          <option value="">すべて</option>
          @foreach($schoolTypeLabels as $value => $label)
            <option value="{{ $value }}" {{ ($filters['school_type'] ?? null) === $value ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="sort" class="block text-xs text-gray-500">並び替え</label>
        <select id="sort" name="sort" class="mt-1 w-full border rounded px-3 py-2 text-sm">
          <option value="rating" {{ ($filters['sort'] ?? 'rating') === 'rating' ? 'selected' : '' }}>平均評価が高い順</option>
          <option value="count" {{ ($filters['sort'] ?? 'rating') === 'count' ? 'selected' : '' }}>レビュー件数が多い順</option>
          <option value="recent" {{ ($filters['sort'] ?? 'rating') === 'recent' ? 'selected' : '' }}>最新の投稿順</option>
        </select>
      </div>

      <div class="col-span-5 flex flex-wrap gap-3 pt-2">
        <button class="px-4 py-2 rounded-lg bg-black text-white text-sm font-semibold">絞り込む</button>
        <a href="{{ route('reviews.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border text-sm text-gray-700">リセット</a>
      </div>
    </form>
  </section>

  @if($groupedSchools->isEmpty())
    <section class="bg-white border rounded-2xl p-6 text-sm text-gray-600">
      該当する公開レビューがまだありません。条件を変えて再度お試しください。
    </section>
  @else
    @foreach($groupedSchools as $prefecture => $schools)
      <section class="space-y-4">
        <div class="flex items-center gap-3">
          <h2 class="text-xl font-semibold">{{ $prefecture }}</h2>
          <span class="text-xs text-gray-500">{{ count($schools) }} 校</span>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          @foreach($schools as $school)
            <article class="bg-white border rounded-2xl p-4 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-lg font-semibold">
                    <a href="{{ route('schools.show', ['id' => $school->school_id]) }}" class="text-blue-600 hover:underline">{{ $school->school_name }}</a>
                  </h3>
                  <p class="text-xs text-gray-500">{{ $schoolTypeLabels[$school->school_type] ?? '学校' }}</p>
                </div>
                <div class="text-right text-xs text-gray-500">
                  <span>最終更新：{{ $school->last_review_date ?? '－' }}</span>
                </div>
              </div>

              <div class="flex items-center gap-3 text-sm">
                <div class="flex items-center gap-1 text-amber-500">
                  @php $avg = $school->avg_rating ?? 0; @endphp
                  @for($i = 1; $i <= 5; $i++)
                    <span>{{ $i <= floor($avg) ? '★' : '☆' }}</span>
                  @endfor
                </div>
                <span class="text-gray-700">{{ $school->avg_rating !== null ? number_format($school->avg_rating, 1) : '－' }}</span>
                <span class="text-gray-500">｜ {{ $school->review_count }} 件</span>
              </div>

              @php
                $latest = $school->latest_review ?? ['pros' => [], 'cons' => [], 'notes' => ''];
              @endphp

              <div class="space-y-2 text-xs text-gray-600">
                @if(!empty($latest['pros']))
                  <p><span class="font-semibold text-gray-700">良かった点:</span> {{ $latest['pros'][0] }}</p>
                @endif
                @if(!empty($latest['cons']))
                  <p><span class="font-semibold text-gray-700">気になった点:</span> {{ $latest['cons'][0] }}</p>
                @endif
                @if(empty($latest['pros']) && empty($latest['cons']) && !empty($latest['notes']))
                  <p class="text-gray-600">{{ \Illuminate\Support\Str::limit($latest['notes'], 80) }}</p>
                @endif
              </div>

              <div>
                <a href="{{ route('schools.show', ['id' => $school->school_id]) }}" class="inline-flex items-center text-sm text-blue-600">学校のレビューを見る →</a>
              </div>
            </article>
          @endforeach
        </div>
      </section>
    @endforeach
  @endif
</main>
</body>
</html>
