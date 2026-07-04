<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #171c1f; font-size: 11px; margin: 0; }
        .kop td { vertical-align: middle; }
        .kop-center { text-align: center; }
        .kop-center .kab { font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .kop-center .pkbm { font-size: 15px; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .kop-center .addr { font-size: 9px; color: #505f76; }
        .logo-box { width: 60px; height: 60px; border: 1px solid #c5c5d7; text-align: center; }
        .logo-box img { max-width: 58px; max-height: 58px; }
        .sep { border-bottom: 3px double #171c1f; height: 6px; margin-bottom: 14px; }
        .title { text-align: center; margin: 16px 0 18px; }
        .title span { font-size: 12px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #171c1f; padding-bottom: 2px; }
        table.ident td { font-size: 11px; padding: 1px 0; }
        table.nilai { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.nilai th, table.nilai td { border: 1px solid #171c1f; padding: 6px; font-size: 10px; }
        table.nilai th { text-align: center; }
        .c { text-align: center; }
        .desc { font-size: 9px; color: #505f76; }
        .catatan { border: 1px solid #171c1f; padding: 10px; min-height: 60px; margin-bottom: 40px; font-size: 10px; }
        .catatan .lbl { font-weight: bold; margin-bottom: 4px; }
        .catatan .isi { color: #505f76; font-style: italic; }
        table.ttd { width: 100%; text-align: center; font-size: 10px; }
        table.ttd td { width: 33%; vertical-align: top; padding: 0 8px; }
        .sign-line { border-bottom: 1px solid #171c1f; height: 60px; }
        .nip { font-size: 9px; color: #757686; }
    </style>
</head>
<body>
    @php
        $semLabel = ($periode?->semester === 'genap') ? 'Genap (2)' : 'Ganjil (1)';
        $logoKabPath = $kop['logo_kab'] ? public_path('storage/' . $kop['logo_kab']) : null;
        $logoPkbmPath = $kop['logo_pkbm'] ? public_path('storage/' . $kop['logo_pkbm']) : null;
    @endphp

    {{-- Kop --}}
    <table class="kop" width="100%">
        <tr>
            <td width="70">
                <div class="logo-box">
                    @if ($logoKabPath && file_exists($logoKabPath))
                        <img src="{{ $logoKabPath }}" alt="">
                    @endif
                </div>
            </td>
            <td class="kop-center">
                <div class="kab">{{ $kop['kabupaten'] }}</div>
                <div class="kab">{{ $kop['dinas'] }}</div>
                <div class="pkbm">{{ $kop['nama_pkbm'] }}</div>
                <div class="addr">{{ $kop['alamat'] }}</div>
                <div class="addr">
                    @if ($kop['telepon']) Telp: {{ $kop['telepon'] }} @endif
                    @if ($kop['telepon'] && $kop['email']) &middot; @endif
                    @if ($kop['email']) Email: {{ $kop['email'] }} @endif
                </div>
            </td>
            <td width="70" align="right">
                <div class="logo-box">
                    @if ($logoPkbmPath && file_exists($logoPkbmPath))
                        <img src="{{ $logoPkbmPath }}" alt="">
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="sep"></div>

    {{-- Judul --}}
    <div class="title"><span>Laporan Hasil Belajar Peserta Didik</span></div>

    {{-- Identitas --}}
    <table class="ident" width="100%">
        <tr>
            <td width="50%"><table><tr><td width="130">Nama Peserta Didik</td><td>: <strong>{{ strtoupper($pd?->nama_lengkap ?? '') }}</strong></td></tr></table></td>
            <td width="50%"><table><tr><td width="120">Kelas / Rombel</td><td>: {{ $rombel?->nama }}</td></tr></table></td>
        </tr>
        <tr>
            <td><table><tr><td width="130">NIPD / NISN</td><td>: {{ $pd?->nipd }} / {{ $pd?->nisn ?: '—' }}</td></tr></table></td>
            <td><table><tr><td width="120">Semester</td><td>: {{ $semLabel }}</td></tr></table></td>
        </tr>
        <tr>
            <td><table><tr><td width="130">Tahun Pelajaran</td><td>: {{ $periode?->tahun_ajaran }}</td></tr></table></td>
            <td></td>
        </tr>
    </table>
    <br>

    {{-- Tabel nilai --}}
    <table class="nilai">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mata Pelajaran</th>
                <th width="70">Nilai Angka</th>
                <th width="60">Predikat</th>
                <th width="220">Deskripsi Kemajuan Belajar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $row['mapel'] }}</td>
                    <td class="c">{{ $row['nilai'] ?? '—' }}</td>
                    <td class="c"><strong>{{ $row['grade'] ?? '—' }}</strong></td>
                    <td class="desc">{{ $row['deskripsi'] ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="c desc">Belum ada nilai mata pelajaran.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Catatan wali --}}
    <div class="catatan">
        <div class="lbl">Catatan Wali Kelas:</div>
        <div class="isi">{{ $catatanWali ? '"' . $catatanWali . '"' : '—' }}</div>
    </div>

    {{-- TTD --}}
    <table class="ttd">
        <tr>
            <td>Mengetahui,<br>Orang Tua/Wali<div class="sign-line"></div><strong>&nbsp;</strong></td>
            <td>
                {{ now()->translatedFormat('d F Y') }}<br>Wali Kelas<div class="sign-line"></div>
                <strong>{{ $waliKelas?->nama_lengkap ?? '(...................)' }}</strong>
                <div class="nip">{{ $waliKelas?->nip ? 'NIP. ' . $waliKelas->nip : '' }}</div>
            </td>
            <td>Mengetahui,<br>Kepala PKBM<div class="sign-line"></div>
                <strong>{{ $kop['kepala_nama'] ?: '(...................)' }}</strong>
                <div class="nip">{{ $kop['kepala_nip'] ? 'NIP. ' . $kop['kepala_nip'] : '' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
