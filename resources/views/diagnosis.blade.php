<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite('resources/css/app.css')
  <title>興味診断｜保護者OCマッチング</title>
</head>
<body class="min-h-screen text-text">
  <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10 space-y-8">
    <header class="space-y-3">
      <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-xs text-muted hover:text-text">← トップへ戻る</a>
      <h1 class="text-2xl font-semibold">興味診断（約3分）</h1>
      <p class="text-sm text-muted leading-relaxed">
        親子で話し合うきっかけになるよう、興味・好きな科目・憧れの人物像をまとめます。入力した内容は診断結果とメモにだけ反映され、あとから編集もできます。
      </p>
    </header>

    @php
      $preferences = config('match_preferences');
      $prefectures = [
        '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
      ];
    @endphp

    <form method="POST" action="{{ route('match.post') }}" class="space-y-10" x-data="{soc:false, sci:false}">
      @csrf

      <section class="space-y-4">
        <div class="space-y-1">
          <h2 class="text-lg font-semibold text-text">Q1 興味のあるジャンルを選んでください（複数可）</h2>
          <p class="text-xs text-muted leading-relaxed">普段の会話でよく出る話題や「最近気になっていること」を思い出しながら選んでみましょう。</p>
        </div>
        <div class="space-y-4">
          @foreach($preferences['categories'] as $index => $category)
            <details class="bg-surface border border-border rounded-2xl overflow-hidden" {{ $loop->first ? 'open' : '' }}>
              <summary class="flex items-center justify-between px-4 py-3 cursor-pointer text-sm font-semibold text-text">
                <span>{{ $category['label'] }}</span>
                <span class="inline-flex items-center gap-1 text-xs text-icon">選択肢を表示 <svg class="w-3 h-3" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
              </summary>
              <div class="px-4 pb-4 pt-2 grid gap-3 sm:grid-cols-2 text-sm">
                @foreach($category['options'] as $option)
                  <label class="flex items-start gap-3 p-3 rounded-2xl border border-border bg-surface hover:border-primary transition text-xs sm:text-sm">
                    <input type="checkbox" name="interests[]" value="{{ $option['id'] }}" class="mt-1 accent-primary" />
                    <span class="space-y-1">
                      <span class="font-semibold block text-text">{{ $option['label'] }}</span>
                      <span class="text-muted leading-relaxed">{{ $option['reason'] }}</span>
                    </span>
                  </label>
                @endforeach
              </div>
            </details>
          @endforeach
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-text">Q2 好きな科目</h2>
        <p class="text-xs text-muted leading-relaxed">テストの得意・不得意だけではなく、「授業を聞いていてワクワクする」「調べてみたい」と感じる科目を選んでください。</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          @foreach(array_keys($preferences['subjects']) as $subject)
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-surface border border-border hover:border-primary transition">
              <input type="checkbox" name="subjects[]" value="{{ $subject }}" class="accent-primary"
                @change="if($event.target.value==='社会'){soc=$event.target.checked}; if($event.target.value==='理科'){sci=$event.target.checked}">
              <span>{{ $subject }}</span>
            </label>
          @endforeach
        </div>
        <div class="space-y-2">
          <template x-if="soc">
            <div class="pl-4 border-l-2 border-accent/60 space-y-2">
              <p class="text-xs text-muted">社会の細分</p>
              @foreach(['地理','歴史','公民'] as $d)
                <label class="inline-flex items-center gap-2 mr-3 text-sm text-text">
                  <input type="checkbox" name="subjects_detail[]" value="{{ $d }}" class="accent-primary"><span>{{ $d }}</span>
                </label>
              @endforeach
            </div>
          </template>
          <template x-if="sci">
            <div class="pl-4 border-l-2 border-accent/60 space-y-2">
              <p class="text-xs text-muted">理科の細分</p>
              @foreach(['物理','化学','生物'] as $d)
                <label class="inline-flex items-center gap-2 mr-3 text-sm text-text">
                  <input type="checkbox" name="subjects_detail[]" value="{{ $d }}" class="accent-primary"><span>{{ $d }}</span>
                </label>
              @endforeach
            </div>
          </template>
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-text">Q3 憧れの人物像（複数選択可）</h2>
        <p class="text-xs text-muted leading-relaxed">思い浮かぶ職業や人物があれば近いカテゴリを選び、自由記入欄にエピソードを書いておくとマッチ理由が丁寧になります。</p>
        <div class="flex flex-wrap gap-2">
          @foreach($preferences['hero_categories'] as $key => $hero)
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-surface border border-border hover:border-primary transition text-sm">
              <input type="checkbox" name="heroes[]" value="{{ $key }}" class="accent-primary"><span>{{ $hero['label'] }}</span>
            </label>
          @endforeach
        </div>
        <textarea name="hero_note" class="w-full border border-border rounded-2xl px-3 py-2 text-sm bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20" rows="2" placeholder="任意：具体的に憧れている人やエピソードがあれば教えてください"></textarea>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-text">Q4 居住している都道府県</h2>
        <p class="text-xs text-muted">旅行時間や距離を考慮したOCがおすすめに出やすくなります（任意）。</p>
        <select name="prefecture" class="w-full border border-border rounded-2xl px-3 py-2 text-sm bg-surface focus:outline-none focus:ring-2 focus:ring-primary/20">
          <option value="">選択してください（任意）</option>
          @foreach($prefectures as $pref)
            <option value="{{ $pref }}">{{ $pref }}</option>
          @endforeach
        </select>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-text">Q5 誰が入力していますか？</h2>
        <p class="text-xs text-muted">あとからマイページで入力者を切り替えられます。初回は保護者の目線でまとめるのがおすすめです。</p>
        <div class="flex flex-wrap gap-3 text-sm">
          <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-surface border border-border hover:border-primary transition">
            <input type="radio" name="entry_role" value="parent" checked class="accent-primary"><span>まずは保護者だけ</span>
          </label>
          <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-surface border border-border hover:border-primary transition">
            <input type="radio" name="entry_role" value="student" class="accent-primary"><span>子どもと一緒に</span>
          </label>
        </div>
      </section>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-border pt-4">
        <p class="text-xs text-muted leading-relaxed">送信後は候補5件とおすすめ理由が表示されます。リンクを共有して、家族や先生とも情報を共有しましょう。</p>
        <button class="btn-primary w-full sm:w-auto">診断結果を見る</button>
      </div>
    </form>
  </main>
</body>
</html>
