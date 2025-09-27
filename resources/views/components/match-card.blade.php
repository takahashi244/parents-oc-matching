@props(['candidate'])
<div class="rounded-2xl shadow p-5 border bg-white">
  <div class="text-lg font-semibold">{{ $candidate['school_name'] }} {{ $candidate['dept_name'] }}</div>
  <div class="mt-2 flex flex-wrap gap-2">
    @foreach($candidate['reasons'] as $r)
      <span class="px-2 py-1 rounded-full text-sm border">{{ $r }}</span>
    @endforeach
  </div>
  <div class="mt-4 flex gap-3">
    <a href="{{ $candidate['detail_url'] }}" class="px-4 py-2 rounded-xl bg-black text-white">OC候補を見る</a>
  </div>
</div>
