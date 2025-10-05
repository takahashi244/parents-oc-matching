@props(['candidate'])
@php
  $hasOc = !empty($candidate['ocev_id']) && $candidate['detail_url'] !== '#';
  $primaryReason = $candidate['reasons'][0] ?? null;
  $otherReasons  = array_slice($candidate['reasons'], 1);
@endphp
<div class="rounded-2xl border border-border bg-surface p-5 space-y-4">
  <div class="flex items-start justify-between gap-3 flex-wrap">
    <div>
      <p class="text-xs text-muted">{{ $candidate['school_name'] }}</p>
      <h3 class="text-lg font-semibold text-text">{{ $candidate['dept_name'] }}</h3>
    </div>
    <div class="text-xs text-muted bg-surfaceMuted border border-border px-2 py-1 rounded-full">総合スコア {{ $candidate['score'] }}</div>
  </div>

  @if($primaryReason)
    <div class="rounded-2xl bg-accent/15 border border-accent/40 px-4 py-3 text-sm text-text leading-relaxed">
      {{ $primaryReason }}
    </div>
  @endif

  @if(!empty($otherReasons))
    <div class="space-y-1">
      <p class="text-xs font-semibold text-muted">ほかのチェックポイント</p>
      <ul class="space-y-1.5 text-sm text-text list-disc list-inside">
        @foreach($otherReasons as $reason)
          <li>{{ $reason }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="pt-1 flex flex-wrap gap-3">
    @if($hasOc)
      <a href="{{ $candidate['detail_url'] }}" class="btn-secondary text-sm">OC候補を見る</a>
    @else
      <span class="px-4 py-2 rounded-full border border-border text-sm text-muted">近日中のOC情報は準備中です</span>
    @endif
  </div>
</div>
