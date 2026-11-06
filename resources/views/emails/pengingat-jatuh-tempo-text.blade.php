Halo, {!! $namaPemilik !!}

Berikut tagihan yang perlu perhatian Anda.
@if (count($hariH) > 0)

JATUH TEMPO HARI INI ({{ count($hariH) }})
@foreach ($hariH as $item)
- {!! $item['nama'] !!} ({!! $item['nomor'] !!}) - {!! $item['jenis'] !!}, sisa {!! $item['sisa'] !!}
  {!! $item['url'] !!}
@endforeach
@endif
@if (count($tujuhHari) > 0)

JATUH TEMPO 7 HARI LAGI ({{ count($tujuhHari) }})
@foreach ($tujuhHari as $item)
- {!! $item['nama'] !!} ({!! $item['nomor'] !!}) - {!! $item['jenis'] !!}, tempo {!! $item['tempo'] !!}, sisa {!! $item['sisa'] !!}
  {!! $item['url'] !!}
@endforeach
@endif

Buka halaman Jatuh Tempo: {{ route('jatuh-tempo.index') }}

Email ini dikirim otomatis oleh BukuTempo.