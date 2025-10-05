<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite('resources/css/app.css')
  <title>共有された診断結果</title>
</head>
<body class="bg-gray-50">
<main class="max-w-3xl mx-auto p-6 space-y-6">
  <section class="bg-white border rounded-2xl p-4 space-y-2">
    <h1 class="text-2xl font-bold">診断結果（共有リンク）</h1>
    <p class="text-sm text-gray-600">このリンクは {{ $expires_at->format('Y/m/d') }} まで有効です。</p>
    <div class="flex flex-col gap-2 pt-2">
      <label class="text-xs text-gray-500" for="share-url">このURLを共有してください</label>
      <input id="share-url" type="text" readonly value="{{ url()->current() }}" class="border rounded px-2 py-1 text-sm bg-gray-100">
    </div>
    <p class="text-sm text-gray-500 pt-2">興味に合いそうな候補を親子で話し合ってみましょう。</p>
  </section>

  <section class="space-y-4">
    @forelse($candidates as $candidate)
      <x-match-card :candidate="$candidate" />
    @empty
      <article class="bg-white border rounded-2xl p-4 text-sm text-gray-600">
        候補が見つかりませんでした。共有リンクの作成者に確認してください。
      </article>
    @endforelse
  </section>

  <section class="bg-white border rounded-2xl p-4 space-y-2 text-sm text-gray-600">
    <p>リンクを受け取った方へ：</p>
    <ul class="list-disc list-inside space-y-1">
      <li>興味に合いそうな候補をメモしておくと、OCに行った後の振り返りがしやすくなります。</li>
      <li>リンクの有効期限は 7 日間です。期限を過ぎると再度共有が必要になります。</li>
      <li>さらに候補を探したい場合は、親御さんと一緒に診断を行うと最新の候補が表示されます。</li>
    </ul>
  </section>
</main>
</body>
</html>
