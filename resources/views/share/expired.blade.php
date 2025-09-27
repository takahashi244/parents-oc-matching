<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite('resources/css/app.css')
  <title>共有リンクの有効期限切れ</title>
</head>
<body class="bg-gray-50">
<main class="max-w-xl mx-auto p-6 space-y-4">
  <section class="bg-white border border-red-200 rounded-2xl p-5 space-y-3">
    <h1 class="text-2xl font-bold text-red-600">共有リンクは利用できません</h1>
    <p class="text-sm text-gray-700">{{ $message ?? 'この共有リンクは有効期限切れか、存在しません。' }}</p>
    <p class="text-sm text-gray-500">再度候補を共有してもらうか、診断をやり直してください。</p>
    <div class="pt-2">
      <a href="{{ url('/') }}" class="px-4 py-2 rounded bg-black text-white text-sm">診断を試す</a>
    </div>
  </section>
</main>
</body>
</html>
