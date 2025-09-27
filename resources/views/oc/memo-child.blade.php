<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite('resources/css/app.css')
  <title>子ども用内見メモ</title>
</head>
<body class="bg-gray-50">
<main class="max-w-3xl mx-auto p-6 space-y-6">
  <header class="space-y-3">
    <a href="{{ route('oc.show', ['id' => $ev->ocev_id]) }}" class="text-sm text-gray-500">← OC詳細へ戻る</a>
    <div>
      <h1 class="text-2xl font-bold">子ども用内見メモ</h1>
      <p class="mt-1 text-sm text-gray-600">感じたことを1〜5で残しておくと、あとで家族で話し合いやすくなります。</p>
    </div>
  </header>

  @if(session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 rounded text-sm">{{ session('ok') }}</div>
  @endif

  <form method="POST" action="{{ route('oc.memo.child.store', ['id' => $ev->ocev_id]) }}" class="space-y-4">
    @csrf
    <input type="hidden" name="ocev_id" value="{{ $ev->ocev_id }}">
    <input type="hidden" name="role" value="student">

    @if($errors->any())
      <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
        <ul class="space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

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
              <option value="{{ $i }}" {{ old('ratings.'.$item['id']) == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
      </div>
    @endforeach

    <div class="flex items-start space-x-2">
      <input type="checkbox" id="agree_terms" name="agree_terms" value="1" class="mt-1" {{ old('agree_terms') ? 'checked' : '' }}>
      <label for="agree_terms" class="text-sm text-gray-700">個人が特定される情報や誹謗中傷を含めないことに同意します</label>
    </div>

    <button class="w-full px-4 py-3 rounded-lg bg-black text-white text-sm font-semibold">メモを保存</button>
  </form>
</main>
</body>
</html>
