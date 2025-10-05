<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite('resources/css/app.css')
  <title>親用内見メモ</title>
</head>
<body class="bg-gray-50">
@php
    $prefill = $prefill ?? null;
    $prefillRatings = $prefill['ratings'] ?? [];
@endphp

<main class="max-w-3xl mx-auto p-6 space-y-6">
  <header class="space-y-3">
    <a href="{{ route('oc.show', ['id' => $ev->ocev_id]) }}" class="text-sm text-gray-500">← OC詳細へ戻る</a>
    <div>
      <h1 class="text-2xl font-bold">親用内見メモ</h1>
      <p class="mt-1 text-sm text-gray-600">気になった項目だけで構いません。評価は1（低い）〜5（高い）で記録しておくと、あとで振り返りやすくなります。</p>
      <p class="text-xs text-gray-500">送信すると確認画面が表示され、内容をチェックしてから公開できます。</p>
    </div>
    @if($prefill)
      <div class="text-xs text-blue-600">※ 確認画面で保存した内容を引き継いでいます。必要に応じて修正してください。</div>
    @endif
  </header>

  @if($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
      <ul class="space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('oc.memo.parent.store', ['id' => $ev->ocev_id]) }}" class="space-y-4">
    @csrf
    @foreach($checklist as $item)
      <div class="bg-white border rounded-2xl p-4 space-y-3">
        <div>
          <span class="font-medium">{{ $item['label'] }}</span>
          <p class="mt-1 text-xs text-gray-500">{{ $item['description'] }}</p>
        </div>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600" for="rating_{{ $item['id'] }}">評価</label>
          <select id="rating_{{ $item['id'] }}" name="ratings[{{ $item['id'] }}]" class="border rounded px-2 py-1">
            <option value="">-</option>
            @for($i = 1; $i <= 5; $i++)
              <option value="{{ $i }}" {{ old('ratings.'.$item['id'], $prefillRatings[$item['id']] ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
      </div>
    @endforeach

    <div class="bg-white border rounded-2xl p-4 space-y-3">
      <label class="block text-sm font-semibold" for="overall_rating">総合評価（1〜5）</label>
      <select id="overall_rating" name="overall_rating" class="border rounded px-2 py-1 w-32">
        <option value="">-</option>
        @for($i = 1; $i <= 5; $i++)
          <option value="{{ $i }}" {{ old('overall_rating', $prefill['overall_rating'] ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
      </select>
    </div>

    <div class="bg-white border rounded-2xl p-4 space-y-3">
      <label class="block text-sm font-semibold" for="good_text">良かった点</label>
      <textarea id="good_text" name="good_text" rows="4" class="w-full border rounded px-3 py-2" placeholder="印象に残った良い点を具体的に書いてください。">{{ old('good_text', $prefill['good_text'] ?? '') }}</textarea>
      </div>

    <div class="bg-white border rounded-2xl p-4 space-y-3">
      <label class="block text-sm font-semibold" for="concern_text">気になった点</label>
      <textarea id="concern_text" name="concern_text" rows="4" class="w-full border rounded px-3 py-2" placeholder="改善してほしい点や不安に感じたことを書いてください。">{{ old('concern_text', $prefill['concern_text'] ?? '') }}</textarea>
    </div>

    <div class="flex items-start space-x-2">
      <input type="checkbox" id="agree_terms" name="agree_terms" value="1" class="mt-1" {{ old('agree_terms') ? 'checked' : '' }}>
      <label for="agree_terms" class="text-sm text-gray-700">個人が特定される情報や誹謗中傷を含めないことに同意します</label>
    </div>

    <button class="btn-primary w-full">レビューを確認する</button>
  </form>
</main>
</body>
</html>
