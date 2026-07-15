<?php

namespace App\Livewire\PesertaDidik\Absensi;

use App\Models\AbsensiDetail;
use App\Models\SesiAbsensi;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Absensi'])]
#[Title('Absensi')]
class TombolHadir extends Component
{
    public function absen(int $sesiId, string $status): void
    {
        $allowed = ['hadir', 'izin', 'sakit'];
        if (!in_array($status, $allowed, true)) return;

        $pd = Auth::user()->pesertaDidik;
        if (!$pd) return;

        $sesi = SesiAbsensi::find($sesiId);
        if (! $sesi) return;

        // Auto-close jika tutup_pada sudah lewat
        if ($sesi->status_sesi === 'terbuka' && $sesi->tutup_pada && $sesi->tutup_pada->isPast()) {
            $sesi->update(['status_sesi' => 'ditutup']);
            $sesi->refresh();
        }

        if ($sesi->status_sesi !== 'terbuka') {
            $this->dispatch('notify', type: 'error', message: 'Sesi absensi sudah ditutup.');
            return;
        }

        // Belum waktunya buka
        if ($sesi->tanggal_buka && $sesi->tanggal_buka->isFuture()) {
            $this->dispatch('notify', type: 'warning', message: 'Sesi belum dibuka. Absensi dimulai pukul ' . $sesi->tanggal_buka->format('H:i') . '.');
            return;
        }

        // Pastikan PD anggota rombel sesi tersebut
        $rombel = $sesi->guruMapelRombel->rombel;
        $isMember = $rombel->pesertaDidikRombel()->where('peserta_didik_id', $pd->id)->exists();
        if (!$isMember) return;

        try {
            $existing = AbsensiDetail::where('sesi_absensi_id', $sesiId)
                ->where('peserta_didik_id', $pd->id)
                ->first();

            if ($existing && !$existing->diubah_manual_oleh) {
                // PD sudah absen sendiri, tidak boleh ubah
                $this->dispatch('notify', type: 'warning', message: 'Anda sudah mengisi absensi untuk sesi ini.');
                return;
            }

            if (!$existing) {
                AbsensiDetail::create([
                    'sesi_absensi_id'    => $sesiId,
                    'peserta_didik_id'   => $pd->id,
                    'status'             => $status,
                    'waktu_klik'         => now(),
                    'diubah_manual_oleh' => null,
                ]);
                $this->dispatch('notify', type: 'success', message: 'Absensi berhasil dicatat.');
            }
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Absen Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal mencatat absensi. Coba lagi.');
        }
    }

    public function render(): View
    {
        $pd = Auth::user()->pesertaDidik;
        $sesiTerbuka = collect();
        $detailHariIni = collect();
        $riwayatMinggu = collect();

        $periode = app(\App\Services\PeriodeService::class)->getSelected();

        if ($pd && $periode) {
            // Rombel PD pada TA yang sedang dilihat saja — sebelumnya mengambil SEMUA
            // rombel lintas TA, sehingga PD bisa melihat/mengisi sesi absensi milik
            // rombel tahun lain.
            $rombelIds = $pd->pesertaDidikRombel()
                ->whereHas('rombel', fn($q) => $q->where('tahun_ajaran', $periode->tahun_ajaran))
                ->pluck('rombel_id');

            // Absensi terikat SEMESTER → selalu batasi ke periode yang dipilih.
            $sesiPeriode = fn($q) => $q->where('periode_ajaran_id', $periode->id)
                ->whereHas('guruMapelRombel', fn($s) => $s->whereIn('rombel_id', $rombelIds));

            // Auto-close sesi yang sudah melewati tutup_pada
            SesiAbsensi::where($sesiPeriode)
                ->whereDate('tanggal', today())
                ->where('status_sesi', 'terbuka')
                ->whereNotNull('tutup_pada')
                ->where('tutup_pada', '<=', now())
                ->update(['status_sesi' => 'ditutup']);

            // Sesi terbuka hari ini yang sudah waktunya buka
            $sesiTerbuka = SesiAbsensi::where($sesiPeriode)
                ->whereDate('tanggal', today())
                ->where('status_sesi', 'terbuka')
                ->where(fn($q) => $q->whereNull('tanggal_buka')->orWhere('tanggal_buka', '<=', now()))
                ->with(['guruMapelRombel.mapel', 'guruMapelRombel.guru'])
                ->get();

            // Detail absensi PD hari ini (semua sesi)
            $sesiHariIniIds = SesiAbsensi::where($sesiPeriode)
                ->whereDate('tanggal', today())
                ->pluck('id');

            $detailHariIni = AbsensiDetail::whereIn('sesi_absensi_id', $sesiHariIniIds)
                ->where('peserta_didik_id', $pd->id)
                ->get()
                ->keyBy('sesi_absensi_id');

            // Riwayat 7 hari terakhir (per hari, ambil status majority atau hadir jika ada)
            $riwayatMinggu = AbsensiDetail::where('peserta_didik_id', $pd->id)
                ->whereHas('sesiAbsensi', fn($q) => $q->whereBetween('tanggal', [today()->subDays(6), today()]))
                ->with('sesiAbsensi')
                ->get()
                ->groupBy(fn($d) => $d->sesiAbsensi->tanggal->format('Y-m-d'));
        }

        return view('livewire.peserta-didik.absensi.tombol-hadir', compact(
            'pd', 'sesiTerbuka', 'detailHariIni', 'riwayatMinggu'
        ));
    }
}
