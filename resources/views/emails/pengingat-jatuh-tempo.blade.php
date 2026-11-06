<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat jatuh tempo</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f0efed;font-family:Arial,Helvetica,sans-serif;color:#2a2a5c;">
@php
    $bagian = [
        [
            'judul' => 'Jatuh tempo hari ini',
            'catatan' => 'Segera tagih atau bayar hari ini.',
            'warna' => '#a3153f',
            'latar' => '#fdeaed',
            'items' => $hariH,
        ],
        [
            'judul' => 'Jatuh tempo 7 hari lagi',
            'catatan' => 'Siapkan penagihan atau pembayarannya dari sekarang.',
            'warna' => '#8a5a00',
            'latar' => '#fdf1d3',
            'items' => $tujuhHari,
        ],
    ];
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;">
    <tr>
        <td style="padding:0 4px 14px;font-size:16px;font-weight:bold;">BukuTempo</td>
    </tr>
    <tr>
        <td style="background:#ffffff;border-radius:18px;padding:24px;">
            <p style="margin:0 0 4px;font-size:15px;font-weight:bold;">Halo, {{ $namaPemilik }}</p>
            <p style="margin:0 0 18px;font-size:13px;line-height:1.5;color:#6b6b86;">Berikut tagihan yang perlu perhatian Anda.</p>

            @foreach ($bagian as $b)
                @continue(count($b['items']) === 0)

                <div style="margin:0 0 6px;padding:8px 12px;border-radius:10px;background:{{ $b['latar'] }};color:{{ $b['warna'] }};font-size:13px;font-weight:bold;">
                    {{ $b['judul'] }} ({{ count($b['items']) }})
                </div>
                <p style="margin:0 0 6px;font-size:12px;color:#6b6b86;">{{ $b['catatan'] }}</p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
                    @foreach ($b['items'] as $item)
                        <tr>
                            <td style="padding:10px 0;border-top:1px solid #eeede9;font-size:13px;">
                                <a href="{{ $item['url'] }}" style="color:#2a2a5c;font-weight:bold;text-decoration:none;">{{ $item['nama'] }}</a><br>
                                <span style="font-size:12px;color:#6b6b86;">{{ $item['nomor'] }} · {{ $item['jenis'] }} · Tempo {{ $item['tempo'] }}</span>
                            </td>
                            <td align="right" style="padding:10px 0;border-top:1px solid #eeede9;font-size:13px;font-weight:bold;white-space:nowrap;">{{ $item['sisa'] }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach

            <a href="{{ route('jatuh-tempo.index') }}" style="display:inline-block;padding:11px 22px;border-radius:999px;background:#2a2a5c;color:#ffffff;font-size:13px;font-weight:bold;text-decoration:none;">Buka halaman Jatuh Tempo</a>
        </td>
    </tr>
    <tr>
        <td style="padding:14px 4px 0;font-size:11px;line-height:1.5;color:#6b6b86;">
            Email ini dikirim otomatis oleh BukuTempo pada H-7 dan hari jatuh tempo. Tagihan yang sudah lunas atau dibatalkan tidak disertakan.
        </td>
    </tr>
</table>
</body>
</html>