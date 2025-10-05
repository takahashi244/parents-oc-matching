<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  @vite('resources/css/app.css')
  <title>あなたへの候補</title>
</head>
<body class="min-h-screen">
  <main class="max-w-3xl mx-auto p-6 space-y-6">
    <header class="space-y-2">
      <h1 class="text-xl font-semibold text-text">あなたへの候補</h1>
      <p class="text-sm text-muted">合わなければ他の候補も見てみよう。共有したい場合は下のボタンからリンクを作成できます。</p>
    </header>

    @if(session('ok'))
      <div class="p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">{{ session('ok') }}</div>
    @endif
    @if(session('error'))
      <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('share.store') }}" class="flex items-center justify-between gap-4">
      @csrf
      <button class="btn-secondary text-sm">共有リンクを作成する（7日間有効）</button>
    </form>

    <div class="space-y-4">
      @foreach($candidates as $c)
        <x-match-card :candidate="$c" />
      @endforeach
    </div>
  </main>
</body>
</html>
