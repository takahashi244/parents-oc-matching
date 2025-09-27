<!doctype html><html lang="ja"><head>
<meta charset="utf-8" /><meta name="viewport" content="width=device-width,initial-scale=1" />
@vite('resources/css/app.css')<title>あなたへの候補</title></head>
<body class="bg-gray-50"><main class="max-w-3xl mx-auto p-6">
<h1 class="text-xl font-bold mb-4">あなたへの候補</h1>
@if(session('ok'))
  <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm">{{ session('ok') }}</div>
@endif
@if(session('error'))
  <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">{{ session('error') }}</div>
@endif
<p class="text-sm text-gray-600 mb-4">合わなければ他の候補も見てみよう。共有したい場合は下のボタンからリンクを作成できます。</p>

<form method="POST" action="{{ route('share.store') }}" class="mb-6">
  @csrf
  <button class="px-4 py-2 rounded bg-black text-white text-sm">共有リンクを作成する（7日間有効）</button>
</form>
<div class="space-y-4">
  @foreach($candidates as $c)
    <x-match-card :candidate="$c" />
  @endforeach
</div>
</main></body></html>
