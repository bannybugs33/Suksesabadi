@extends('layouts.app')

@section('content')
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $greeting }}, Pak {{ $user->first_name }}</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Berikut kondisi utang-piutang {{ $user->store_name }} per {{ now()->isoFormat('D MMMM Y') }}.
            </p>
        </div>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
            <x-icon name="plus" class="size-4" /> Tambah kuitansi
        </a>
    </div>

    {{-- Banner peringatan --}}
    @if ($due['count'] > 0)
        <div class="mt-6 flex flex-wrap items-center gap-4 rounded-2xl border border-brand-200 bg-brand-50 p-4">
            <span class="grid size-11 place-items-center rounded-xl bg-brand-500 text-white"><x-icon name="clock" /></span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold">Peringatan jatuh tempo</p>
                <p class="text-sm text-ink-soft">
                    Pengingat aktif mulai H-3. Ada {{ $due['count'] }} utang dan piutang yang perlu perhatian hari ini.
                </p>
            </div>
            <a href="#" class="rounded-full border border-brand-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-brand-500">Lihat detail</a>
        </div>
    @endif

    {{-- Kartu statistik --}}
    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Sisa piutang pelanggan" icon="arrow-down-left" :note="$receivable['count'] . ' kuitansi pelanggan'">
            @rupiah($receivable['remaining'])
        </x-stat-card>

        <x-stat-card title="Sisa utang supplier" icon="arrow-up-right"
                     icon-class="bg-brand-50 text-brand-500" note-class="text-brand-500"
                     :note="$payable['count'] . ' kuitansi supplier'">
            @rupiah($payable['remaining'])
        </x-stat-card>

        <x-stat-card title="Jatuh tempo" icon="clock"
                     icon-class="bg-amber-100 text-amber-600" note-class="text-amber-700"
                     :note="$due['late'] . ' terlambat • ' . $due['tomorrow'] . ' besok'">
            {{ $due['count'] }} kuitansi
        </x-stat-card>

        <x-stat-card title="Lunas bulan ini" icon="check-circle"
                     icon-class="bg-brand-50 text-brand-500" note-class="text-brand-500"
                     :note="$settled['count'] . ' pembayaran selesai'">
            @rupiah($settled['amount'])
        </x-stat-card>
    </div>

    {{-- Grafik + status pembayaran --}}
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <section class="relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Arus utang &amp; piutang</h2>
                <span class="text-xs font-medium text-brand-500">{{ now()->isoFormat('MMMM Y') }}</span>
            </div>
            <div class="mt-3 flex gap-5 text-[11px] text-ink-soft">
                <span class="flex items-center gap-1.5"><i class="size-2 rounded-sm bg-slate-500"></i> Piutang masuk</span>
                <span class="flex items-center gap-1.5"><i class="size-2 rounded-sm bg-brand-500"></i> Utang keluar</span>
            </div>

            <div class="mt-4 flex items-end gap-4 overflow-x-auto pb-1">
                @foreach ($cashflow['months'] as $m)
                    <div class="flex min-w-10 flex-1 flex-col items-center">
                        <div class="flex items-end gap-1.5">
                            <div class="w-6 rounded-t bg-slate-500 sm:w-9" style="height: {{ round($m['in'] / $cashflow['max'] * 140) }}px"></div>
                            <div class="w-6 rounded-t bg-brand-500 sm:w-9" style="height: {{ round($m['out'] / $cashflow['max'] * 140) }}px"></div>
                        </div>
                        <span class="mt-2 text-[10px] text-ink-soft">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Popup detail pengingat --}}
            @if ($reminder)
                <div id="reminder" class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl lg:absolute lg:right-5 lg:top-16 lg:mt-0 lg:w-80">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold">Detail pengingat</p>
                            <p class="text-[11px] text-ink-soft">{{ $due['late'] }} terlambat · {{ $due['tomorrow'] }} jatuh tempo besok</p>
                        </div>
                        <button type="button" id="closeReminder" aria-label="Tutup"
                                class="grid size-7 place-items-center rounded-full border border-slate-200 text-ink-soft">
                            <x-icon name="x" class="size-3.5" />
                        </button>
                    </div>
                    <div class="mt-3 flex items-center gap-3 rounded-xl bg-brand-50 p-3">
                        <span class="grid size-9 place-items-center rounded-lg bg-brand-500 text-white"><x-icon name="clock" class="size-4" /></span>
                        <div class="min-w-0 flex-1 text-xs">
                            <p class="font-semibold">H-{{ $reminder->days_left }} · {{ $reminder->due_date->isoFormat('D MMM Y') }}</p>
                            <p class="truncate text-[10px] text-ink-soft">{{ $reminder->party->name }} • {{ $reminder->code }}</p>
                        </div>
                        <span class="text-xs font-semibold text-brand-500">@rupiah($reminder->remaining)</span>
                    </div>
                    <a href="#" class="mt-3 block rounded-xl bg-brand-500 py-2.5 text-center text-sm font-semibold text-white hover:bg-brand-600">Lihat detail</a>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Status pembayaran</h2>
                <a href="#" class="text-xs font-medium text-brand-500">Lihat rincian</a>
            </div>
            @php
                $bar = ['lunas' => 'bg-slate-500', 'cicilan' => 'bg-sky-700', 'dp' => 'bg-amber-600', 'belum_lunas' => 'bg-brand-500'];
            @endphp
            <div class="mt-4 space-y-4">
                @foreach ($status['items'] as $s)
                    <div>
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold">{{ $s['label'] }}</span>
                            <span class="text-ink-soft">{{ $s['count'] }} kuitansi · {{ $s['percent'] }}%</span>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ $bar[$s['key']] }}" style="width: {{ $s['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs">
                <span class="text-ink-soft">Total nilai tercatat</span>
                <span class="font-semibold">@rupiah($status['amount'])</span>
            </div>
        </section>
    </div>

    {{-- Perlu perhatian + aktivitas --}}
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between px-5 py-4">
                <h2 class="font-semibold">Perlu perhatian</h2>
                <a href="#" class="text-xs font-medium text-brand-500">Lihat semua</a>
            </div>
            <ul class="divide-y divide-slate-100 border-t border-slate-100">
                @forelse ($attention as $r)
                    <li class="flex items-center gap-3 px-5 py-3.5">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg {{ $r->is_overdue ? 'bg-brand-100 text-brand-500' : 'bg-amber-100 text-amber-600' }}">
                            <x-icon name="clock" class="size-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $r->party->name }}</p>
                            <p class="text-[11px] text-ink-soft">{{ $r->code }} • Tempo {{ $r->due_date->isoFormat('D MMM Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">@rupiah($r->remaining)</p>
                            <p class="text-[11px] {{ $r->is_overdue ? 'text-brand-500' : 'text-amber-600' }}">{{ $r->due_label }}</p>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-soft">Tidak ada kuitansi yang perlu perhatian.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Aktivitas terbaru</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @forelse ($activities as $a)
                    @php
                        $icon = $a->icon[0];
                        $cls = $a->icon[1];
                    @endphp
                    <li class="flex gap-3 py-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full {{ $cls }}"><x-icon :name="$icon" class="size-4" /></span>
                        <div class="min-w-0 text-xs">
                            <p class="font-semibold">{{ $a->title }}</p>
                            <p class="text-ink-soft">{{ $a->description }}</p>
                            <p class="mt-0.5 text-[10px] text-ink-soft/80">{{ $a->when_label }}</p>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-ink-soft">Belum ada aktivitas.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('closeReminder')?.addEventListener('click', () =>
        document.getElementById('reminder').remove());
</script>
@endpush
