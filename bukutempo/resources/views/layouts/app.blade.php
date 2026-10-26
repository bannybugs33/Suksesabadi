<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - BukuTempo</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] font-sans text-ink antialiased">
@php
    $authUser = auth()->user();
    $menu = [
        ['Dashboard', 'layout-dashboard', 'dashboard'],
        ['Arsip Kuitansi', 'archive', null],
        ['Kuitansi Baru', 'file-plus', null],
        ['Rekapitulasi', 'bar-chart', null],
    ];
@endphp

<div class="min-h-screen lg:flex">
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 flex w-60 -translate-x-full flex-col bg-brand-700 p-4 text-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <div class="flex items-center gap-3 px-1 py-2">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-500"><x-icon name="book-open" /></span>
            <div class="leading-tight">
                <p class="font-semibold">BukuTempo</p>
                <p class="text-[9px] tracking-widest text-white/70">CATAT • INGAT • LUNAS</p>
            </div>
        </div>

        <p class="mt-7 px-3 text-[10px] font-semibold tracking-wider text-white/60">MENU UTAMA</p>
        <nav class="mt-2 space-y-1">
            @foreach ($menu as [$label, $icon, $route])
                @php $active = $route && request()->routeIs($route); @endphp
                <a href="{{ $route ? route($route) : '#' }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                          {{ $active ? 'bg-white/15' : 'text-white/80 hover:bg-white/10' }}">
                    <x-icon :name="$icon" class="size-4" /> {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-8 rounded-xl bg-white/10 p-3 text-[11px]">
            <p class="flex items-center gap-1.5 font-medium"><span class="size-1.5 rounded-full bg-brand-200"></span> Data tersinkron</p>
            <p class="mt-2 text-white/70">Terakhir diperbarui<br>{{ now()->isoFormat('D MMM Y, HH.mm') }}</p>
        </div>

        <div class="mt-auto flex items-center gap-3 px-1 pt-4">
            <span class="grid size-8 place-items-center rounded-full bg-white text-[11px] font-bold text-brand-700">{{ $authUser->initials }}</span>
            <div class="min-w-0 flex-1 leading-tight">
                <p class="truncate text-xs font-semibold">{{ $authUser->name }}</p>
                <p class="truncate text-[10px] text-white/70">{{ ucfirst($authUser->role) }} • {{ $authUser->store_name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="text-white/80 hover:text-white">
                    <x-icon name="log-out" class="size-4" />
                </button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-3 p-4 lg:hidden">
            <button id="menuBtn" class="rounded-lg border border-slate-200 bg-white p-2" aria-label="Buka menu">
                <x-icon name="menu" />
            </button>
            <span class="font-semibold text-brand-700">BukuTempo</span>
        </div>

        <main class="mx-auto max-w-6xl p-5 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggleSidebar = () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    };
    document.getElementById('menuBtn')?.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
</script>
@stack('scripts')
</body>
</html>
