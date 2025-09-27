<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  @vite('resources/css/app.css')
  <title>レビュー管理</title>
</head>
<body class="bg-gray-100">
<main class="max-w-5xl mx-auto p-6 space-y-6">
  <header class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold">レビュー管理（目視用）</h1>
      <p class="text-sm text-gray-600">公開状態の切り替えや削除を行うと、moderation_logs.csv に記録されます。</p>
    </div>
    <div class="flex gap-2 text-sm">
      <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-3 py-2 rounded border {{ $status === 'pending' ? 'bg-black text-white' : 'bg-white text-gray-700' }}">未公開</a>
      <a href="{{ route('admin.reviews.index', ['status' => 'published']) }}" class="px-3 py-2 rounded border {{ $status === 'published' ? 'bg-black text-white' : 'bg-white text-gray-700' }}">公開中</a>
      <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" class="px-3 py-2 rounded border {{ $status === 'all' ? 'bg-black text-white' : 'bg-white text-gray-700' }}">すべて</a>
    </div>
  </header>

  @if(session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 rounded text-sm">{{ session('ok') }}</div>
  @endif
  @if(session('error'))
    <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">{{ session('error') }}</div>
  @endif

  <section class="space-y-4">
    @forelse($items as $item)
      <article class="bg-white border rounded-2xl p-4 space-y-3">
        <header class="flex flex-wrap justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold">{{ $item->school_name }} {{ $item->dept_name }}</h2>
            <p class="text-xs text-gray-500">rev_id: {{ $item->rev_id }}／{{ $item->author_role === 'parent' ? '保護者' : '子ども' }}／{{ $item->created_at }}</p>
          </div>
          <div class="flex items-center gap-2 text-sm">
            <span class="px-2 py-1 rounded border {{ $item->is_published ? 'bg-green-50 border-green-200 text-green-700' : 'bg-yellow-50 border-yellow-200 text-yellow-700' }}">{{ $item->is_published ? '公開中' : '未公開' }}</span>
            <span class="text-amber-500">評価: {{ $item->rating ?? '－' }}</span>
          </div>
        </header>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
          <div>
            <h3 class="font-semibold text-gray-700">Pros</h3>
            <ul class="list-disc list-inside space-y-1">
              @forelse(json_decode($item->pros ?? '[]', true) as $pros)
                <li>{{ $pros }}</li>
              @empty
                <li class="text-gray-400">（なし）</li>
              @endforelse
            </ul>
          </div>
          <div>
            <h3 class="font-semibold text-gray-700">Cons</h3>
            <ul class="list-disc list-inside space-y-1">
              @forelse(json_decode($item->cons ?? '[]', true) as $cons)
                <li>{{ $cons }}</li>
              @empty
                <li class="text-gray-400">（なし）</li>
              @endforelse
            </ul>
          </div>
        </div>

        @if($item->notes)
          <div class="bg-gray-50 border rounded p-3 text-sm whitespace-pre-wrap">{{ $item->notes }}</div>
        @endif

        <form method="POST" action="{{ route('admin.reviews.moderate', ['rev_id' => $item->rev_id]) }}" class="flex flex-wrap items-center gap-3 text-sm">
          @csrf
          @method('PATCH')
          <label class="flex-1 min-w-[200px]">
            <span class="block text-xs text-gray-500">メモ（任意）</span>
            <input type="text" name="reason" class="w-full border rounded px-2 py-1" placeholder="対応理由や注意事項を記録" />
          </label>
          <div class="flex gap-2">
            <button name="action" value="publish" class="px-3 py-2 rounded bg-green-600 text-white">公開</button>
            <button name="action" value="unpublish" class="px-3 py-2 rounded bg-yellow-600 text-white">非公開</button>
            <button name="action" value="delete" class="px-3 py-2 rounded bg-red-600 text-white" onclick="return confirm('本当に削除しますか？');">削除</button>
          </div>
        </form>
      </article>
    @empty
      <p class="text-sm text-gray-500">該当するレビューはありません。</p>
    @endforelse
  </section>

  <div>
    {{ $items->links() }}
  </div>
</main>
</body>
</html>
