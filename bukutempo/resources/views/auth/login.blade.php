<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - BukuTempo</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] font-sans text-ink antialiased">
<div class="grid min-h-screen lg:grid-cols-[42%_1fr]">

    {{-- Panel kiri --}}
    <section class="hidden flex-col justify-between bg-brand-700 p-12 text-white lg:flex">
        <div class="flex items-center gap-3">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-500"><x-icon name="book-open" /></span>
            <div class="leading-tight">
                <p class="font-semibold">BukuTempo</p>
                <p class="text-[9px] tracking-widest text-white/70">CATAT • INGAT • LUNAS</p>
            </div>
        </div>

        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-[10px] font-medium tracking-wide">
                <span class="size-1.5 rounded-full bg-brand-200"></span> PENCATATAN TOKO YANG TENANG
            </span>
            <h1 class="mt-6 text-4xl font-medium leading-tight xl:text-[44px]">
                Utang–piutang rapi,<br>tempo tak lagi terlewat.
            </h1>
            <p class="mt-5 max-w-md text-sm leading-relaxed text-white/75">
                Simpan kuitansi, pantau pembayaran, dan dapatkan pengingat jatuh tempo dalam satu tempat yang mudah dipahami.
            </p>
        </div>

        {{-- Kartu dekoratif --}}
        <div class="rounded-3xl bg-white/10 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] tracking-wide text-white/70">PIUTANG BULAN INI</p>
                    <p class="mt-1 text-2xl">Rp24,45 jt</p>
                </div>
                <span class="flex items-center gap-1 rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold">
                    <x-icon name="trending-up" class="size-3" /> +8,4%
                </span>
            </div>
            <div class="mt-6 flex h-20 items-end gap-3">
                @foreach ([40, 55, 45, 65, 52, 75, 62, 85] as $i => $h)
                    <div class="flex-1 rounded-t-md {{ $i === 6 ? 'bg-brand-500' : 'bg-white/25' }}" style="height: {{ $h }}%"></div>
                @endforeach
            </div>
            <div class="mt-3 flex items-center gap-3 rounded-xl bg-white p-3 text-ink">
                <span class="grid size-9 place-items-center rounded-lg bg-amber-100 text-amber-600"><x-icon name="clock" class="size-4" /></span>
                <div class="flex-1 text-xs">
                    <p class="font-semibold">4 kuitansi perlu perhatian</p>
                    <p class="text-[10px] text-ink-soft">Rp20.850.000 belum diselesaikan</p>
                </div>
                <span class="text-xs font-semibold text-brand-500">Lihat</span>
            </div>
        </div>

        <p class="text-[11px] text-white/60">© {{ date('Y') }} BukuTempo · Dibuat untuk usaha Indonesia</p>
    </section>

    {{-- Panel kanan --}}
    <section class="flex flex-col items-center justify-center p-6">
        <div class="mb-8 flex items-center gap-3 lg:hidden">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-500 text-white"><x-icon name="book-open" /></span>
            <span class="font-semibold">BukuTempo</span>
        </div>

        <div class="w-full max-w-[400px] rounded-3xl border border-slate-100 bg-white p-7 shadow-xl shadow-slate-200/60">
            <h2 class="text-2xl font-medium">Selamat datang kembali</h2>
            <p class="mt-2 text-xs text-ink-soft">Masuk sebagai owner untuk mengelola arsip dan pembayaran toko.</p>

            @if ($errors->any())
                <div role="alert" class="mt-5 flex gap-3 rounded-xl border border-brand-200 bg-brand-100/70 p-3.5 text-brand-700">
                    <x-icon name="alert-circle" class="mt-0.5 size-4 shrink-0 text-brand-500" />
                    <div>
                        <p class="text-xs font-semibold">
                            {{ $errors->has('credentials') ? 'Kredensial tidak sesuai' : 'Periksa kembali isian Anda' }}
                        </p>
                        <p class="mt-0.5 text-[11px] leading-snug">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="username" class="mb-1.5 block text-xs font-semibold">Username</label>
                    <div class="relative">
                        <x-icon name="user" class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-ink-soft" />
                        <input id="username" name="username" type="text" value="{{ old('username') }}"
                               required autofocus autocomplete="username"
                               class="w-full rounded-xl border border-slate-300 py-2.5 pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    </div>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="text-xs font-semibold">Password</label>
                        <a href="#" class="text-[11px] font-semibold text-brand-500">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <x-icon name="lock" class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-ink-soft" />
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               class="w-full rounded-xl border py-2.5 pl-10 pr-11 text-sm outline-none focus:ring-2 focus:ring-brand-500/20
                                      {{ $errors->any() ? 'border-brand-500' : 'border-slate-300 focus:border-brand-500' }}">
                        <button type="button" id="togglePw" aria-label="Tampilkan password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-soft">
                            <x-icon name="eye" class="size-4" data-icon="show" />
                            <x-icon name="eye-off" class="hidden size-4" data-icon="hide" />
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs text-ink-soft">
                    <input type="checkbox" name="remember" class="size-3.5 rounded border-slate-300 accent-brand-500">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                    Masuk ke BukuTempo <x-icon name="arrow-right" class="size-4" />
                </button>
            </form>

            <p class="mt-5 text-center text-[10px] text-ink-soft">Dilindungi dengan koneksi aman dan terenkripsi</p>
        </div>

        <p class="mt-6 text-[11px] text-ink-soft">Butuh bantuan? Hubungi bantuan@bukutempo.id</p>
    </section>
</div>

<script>
    const pw = document.getElementById('password');
    document.getElementById('togglePw').addEventListener('click', (e) => {
        const reveal = pw.type === 'password';
        pw.type = reveal ? 'text' : 'password';
        e.currentTarget.querySelector('[data-icon="show"]').classList.toggle('hidden', reveal);
        e.currentTarget.querySelector('[data-icon="hide"]').classList.toggle('hidden', !reveal);
    });
</script>
</body>
</html>
