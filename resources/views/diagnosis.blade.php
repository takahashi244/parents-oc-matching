<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite('resources/css/app.css')
  <title>興味診断｜保護者OCマッチング</title>
</head>
<body class="bg-gray-50 text-gray-900">
  <main class="max-w-3xl mx-auto px-6 py-12 space-y-8">
    <header class="space-y-2">
      <a href="{{ url('/') }}" class="text-xs text-gray-500">← トップへ戻る</a>
      <h1 class="text-2xl font-bold">興味診断（約3分）</h1>
      <p class="text-sm text-gray-600">入力した内容はマッチング結果とメモ以外には使いません。あとから子ども用アカウントで編集することもできます。</p>
    </header>

    <form method="POST" action="{{ route('match.post') }}" class="space-y-8" x-data="{soc:false, sci:false}">
      @csrf

      <section class="space-y-3">
        <h2 class="font-semibold">Q1 あてはまる興味を選んでください（複数可）</h2>
        <div class="flex flex-wrap gap-2">
          @foreach(['music'=>'音楽','anime'=>'アニメ・マンガ','game_cg'=>'ゲーム/CG','english_global'=>'英語/外国','math_data'=>'数学','history_social'=>'歴史','sports'=>'スポーツ','medical'=>'医療/看護','design_art'=>'デザイン/アート','programming'=>'プログラミング/IT','business'=>'起業/ビジネス','media'=>'映像・メディア'] as $slug=>$label)
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-white border hover:border-black transition">
              <input type="checkbox" name="tags[]" value="{{ $slug }}" class="accent-black"><span>{{ $label }}</span>
            </label>
          @endforeach
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="font-semibold">Q2 尊敬している人や憧れ</h2>
        <input name="hero" class="w-full border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black/20" placeholder="例：○○選手 / △△起業家 / ＊＊アーティスト" />
      </section>

      <section class="space-y-3">
        <h2 class="font-semibold">Q3 好きな科目</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          @foreach(['国語','英語','数学','理科','社会','芸術','情報','保健体育'] as $subj)
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border hover:border-black transition">
              <input type="checkbox" name="subjects[]" value="{{ $subj }}" class="accent-black"
                @change="if($event.target.value==='社会'){soc=$event.target.checked}; if($event.target.value==='理科'){sci=$event.target.checked}">
              <span>{{ $subj }}</span>
            </label>
          @endforeach
        </div>
        <div class="mt-3 space-y-2">
          <template x-if="soc">
            <div class="pl-4 border-l-2 border-amber-300 space-y-2">
              <p class="text-xs text-gray-600">社会の細分</p>
              @foreach(['地理','歴史','公共'] as $d)
                <label class="inline-flex items-center gap-2 mr-3 text-sm">
                  <input type="checkbox" name="subjects_detail[]" value="{{ $d }}" class="accent-black"><span>{{ $d }}</span>
                </label>
              @endforeach
            </div>
          </template>
          <template x-if="sci">
            <div class="pl-4 border-l-2 border-amber-300 space-y-2">
              <p class="text-xs text-gray-600">理科の細分</p>
              @foreach(['物理','化学','生物'] as $d)
                <label class="inline-flex items-center gap-2 mr-3 text-sm">
                  <input type="checkbox" name="subjects_detail[]" value="{{ $d }}" class="accent-black"><span>{{ $d }}</span>
                </label>
              @endforeach
            </div>
          </template>
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="font-semibold">Q4 誰が入力していますか？</h2>
        <div class="flex flex-wrap gap-4 text-sm">
          <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border hover:border-black transition">
            <input type="radio" name="entry_role" value="parent" checked class="accent-black"><span>まずは保護者だけ</span>
          </label>
          <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border hover:border-black transition">
            <input type="radio" name="entry_role" value="student" class="accent-black"><span>子どもと一緒に</span>
          </label>
        </div>
      </section>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-xs text-gray-500">送信すると、候補5件＋理由チップが表示されます。診断結果は共有リンクでも渡せます。</p>
        <button class="px-5 py-3 rounded-xl bg-black text-white font-semibold shadow-lg shadow-black/10">次へ（マッチを見る）</button>
      </div>
    </form>
  </main>
</body>
</html>
