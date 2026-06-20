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
        if (!$sesi || $sesi->status_sesi !== 'terbuka') {
            $this->dispatch('notify', type: 'error', message: 'Sesi absensi sudah ditutup.');
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

        if ($pd) {
            // Rombel PD
            $rombelIds = $pd->pesertaDidikRombel()->pluck('rombel_id');

            // Sesi terbuka hari ini untuk rombel PD
            $sesiTerbuka = SesiAbsensi::whereHas('guruMapelRombel', fn($q) => $q->whereIn('rombel_id', $rombelIds))
                ->whereDate('tanggal', today())
                ->where('status_sesi', 'terbuka')
                ->with(['guruMapelRombel.mapel', 'guruMapelRombel.guru'])
                ->get();

            // Detail absensi PD hari ini (semua sesi)
            $sesiHariIniIds = SesiAbsensi::whereHas('guruMapelRombel', fn($q) => $q->whereIn('rombel_id', $rombelIds))
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
