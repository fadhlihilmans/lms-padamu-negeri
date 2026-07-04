<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\PesertaDidik;
use App\Models\PesertaDidikAlamat;
use App\Models\PesertaDidikOrtu;
use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use App\Models\Tingkat;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportPesertaDidikService
{
    /**
     * Validasi saja, tanpa menyimpan ke database. Dipakai untuk pratinjau.
     */
    public function preview(UploadedFile $file, int $periodeId): array
    {
        return $this->process($file, $periodeId, commit: false);
    }

    /**
     * Validasi lalu simpan baris yang valid ke database.
     */
    public function import(UploadedFile $file, int $periodeId): array
    {
        return $this->process($file, $periodeId, commit: true);
    }

    private function process(UploadedFile $file, int $periodeId, bool $commit): array
    {
        $sheets = Excel::toArray([], $file);
        if (empty($sheets)) {
            return [['row' => '-', 'status' => 'gagal', 'nama' => '-', 'nipd' => '-', 'rombel' => '-', 'alasan' => 'File Excel kosong atau tidak dapat dibaca.']];
        }

        $rows = $sheets[0]; // sheet pertama
        array_shift($rows); // buang baris header (baris 1)

        $results  = [];
        $rowIndex = 2; // mulai dari baris 2 (setelah header)
        $seenNipd = [];
        $seenNisn = [];

        foreach ($rows as $row) {
            // Normalisasi: pastikan array minimal 26 elemen
            $row = array_pad(array_values($row), 26, null);

            // Hentikan jika seluruh kolom kosong
            if (empty(array_filter(array_map('strval', $row)))) {
                break;
            }

            $nipd        = trim((string) ($row[1] ?? ''));
            $nisn        = trim((string) ($row[2] ?? ''));
            $nik         = trim((string) ($row[3] ?? ''));
            $namaLengkap = trim((string) ($row[4] ?? ''));
            $jenisKelamin = strtoupper(trim((string) ($row[5] ?? '')));
            $tempatLahir = trim((string) ($row[6] ?? ''));
            $tanggalLahirRaw = $row[7] ?? null;
            $agama       = trim((string) ($row[8] ?? ''));
            $noHp        = trim((string) ($row[9] ?? ''));
            // kolom K (index 10) = Email — tidak disimpan di DB
            $namaWilayah = trim((string) ($row[11] ?? ''));
            $namaPaket   = trim((string) ($row[12] ?? ''));
            $namaTingkat = trim((string) ($row[13] ?? ''));
            $alamat      = trim((string) ($row[14] ?? ''));
            $rt          = trim((string) ($row[15] ?? ''));
            $rw          = trim((string) ($row[16] ?? ''));
            $dusun       = trim((string) ($row[17] ?? ''));
            $kelurahan   = trim((string) ($row[18] ?? ''));
            $kecamatan   = trim((string) ($row[19] ?? ''));
            $kodePos     = trim((string) ($row[20] ?? ''));
            $namaAyah    = trim((string) ($row[21] ?? ''));
            $noHpAyah    = trim((string) ($row[22] ?? ''));
            $namaIbu     = trim((string) ($row[23] ?? ''));
            $noHpIbu     = trim((string) ($row[24] ?? ''));
            $namaWali    = trim((string) ($row[25] ?? ''));

            // ── Validasi wajib ──────────────────────────────────────────
            if ($nipd === '') {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'NIPD (kolom B) wajib diisi.');
                $rowIndex++;
                continue;
            }
            if ($namaLengkap === '') {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'Nama Lengkap (kolom E) wajib diisi.');
                $rowIndex++;
                continue;
            }
            if (!in_array($jenisKelamin, ['L', 'P'], true)) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "Jenis Kelamin (kolom F) harus 'L' atau 'P', nilai: '$jenisKelamin'.");
                $rowIndex++;
                continue;
            }

            // ── Cek duplikat NIPD dalam file yang sama ──────────────────
            if (in_array($nipd, $seenNipd, true)) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "NIPD '$nipd' duplikat di dalam file.");
                $rowIndex++;
                continue;
            }

            // ── Cek duplikat NIPD di database ───────────────────────────
            if (PesertaDidik::where('nipd', $nipd)->exists()) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "NIPD '$nipd' sudah terdaftar di sistem.");
                $rowIndex++;
                continue;
            }

            // ── Cek duplikat NISN (jika diisi) ──────────────────────────
            if ($nisn !== '' && in_array($nisn, $seenNisn, true)) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "NISN '$nisn' duplikat di dalam file.");
                $rowIndex++;
                continue;
            }
            if ($nisn !== '' && PesertaDidik::where('nisn', $nisn)->exists()) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "NISN '$nisn' sudah terdaftar di sistem.");
                $rowIndex++;
                continue;
            }

            // ── Cari master Wilayah ──────────────────────────────────────
            if ($namaWilayah === '') {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'Wilayah (kolom L) wajib diisi.');
                $rowIndex++;
                continue;
            }
            $wilayah = Wilayah::whereRaw('LOWER(nama) = ?', [strtolower($namaWilayah)])->first();
            if (!$wilayah) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "Wilayah '$namaWilayah' tidak ditemukan di master data.");
                $rowIndex++;
                continue;
            }

            // ── Cari master Paket (toleransi "C" atau "Paket C") ─────────
            if ($namaPaket === '') {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'Paket (kolom M) wajib diisi.');
                $rowIndex++;
                continue;
            }
            $paket = Paket::whereRaw('LOWER(nama) = ?', [strtolower($namaPaket)])
                ->orWhereRaw('LOWER(nama) = ?', [strtolower('Paket ' . $namaPaket)])
                ->first();
            if (!$paket) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "Paket '$namaPaket' tidak ditemukan di master data.");
                $rowIndex++;
                continue;
            }

            // ── Cari master Tingkat (toleransi "10" atau "Kelas 10") ─────
            if ($namaTingkat === '') {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'Tingkat (kolom N) wajib diisi.');
                $rowIndex++;
                continue;
            }
            $tingkat = Tingkat::where('paket_id', $paket->id)
                ->where(fn($q) => $q
                    ->whereRaw('LOWER(nama) = ?', [strtolower($namaTingkat)])
                    ->orWhereRaw('LOWER(nama) = ?', [strtolower('Kelas ' . $namaTingkat)])
                )->first();
            if (!$tingkat) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "Tingkat '$namaTingkat' tidak ditemukan untuk Paket '$namaPaket'.");
                $rowIndex++;
                continue;
            }

            // ── Cari Rombel ──────────────────────────────────────────────
            $rombel = Rombel::where('wilayah_id', $wilayah->id)
                ->where('paket_id', $paket->id)
                ->where('tingkat_id', $tingkat->id)
                ->where('periode_ajaran_id', $periodeId)
                ->first();
            if (!$rombel) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap,
                    "Tidak ditemukan Rombel untuk kombinasi Wilayah=$namaWilayah, Paket=$namaPaket, Tingkat=$namaTingkat pada periode ini.");
                $rowIndex++;
                continue;
            }

            // ── Cek duplikat keanggotaan rombel ─────────────────────────
            if (PesertaDidikRombel::where('rombel_id', $rombel->id)
                ->whereHas('pesertaDidik', fn($q) => $q->where('nipd', $nipd))
                ->exists()
            ) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, "NIPD '$nipd' sudah terdaftar di Rombel tersebut.");
                $rowIndex++;
                continue;
            }

            $seenNipd[] = $nipd;
            if ($nisn !== '') {
                $seenNisn[] = $nisn;
            }

            if (!$commit) {
                $results[] = [
                    'row'    => $rowIndex,
                    'status' => 'valid',
                    'nipd'   => $nipd,
                    'nama'   => $namaLengkap,
                    'rombel' => $rombel->nama,
                    'alasan' => null,
                ];
                $rowIndex++;
                continue;
            }

            // ── Parse tanggal lahir ──────────────────────────────────────
            $tanggalLahir = null;
            if ($tanggalLahirRaw !== null && $tanggalLahirRaw !== '') {
                try {
                    // Excel sering mengirim date sebagai integer (serial date) atau string
                    if (is_numeric($tanggalLahirRaw)) {
                        $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahirRaw)->format('Y-m-d');
                    } else {
                        $tanggalLahir = Carbon::parse((string) $tanggalLahirRaw)->format('Y-m-d');
                    }
                } catch (\Throwable) {
                    $tanggalLahir = null;
                }
            }

            // ── Insert ke DB (transaksional) ─────────────────────────────
            try {
                DB::transaction(function () use (
                    $nipd, $nisn, $nik, $namaLengkap, $jenisKelamin,
                    $tempatLahir, $tanggalLahir, $agama, $noHp,
                    $alamat, $rt, $rw, $dusun, $kelurahan, $kecamatan, $kodePos,
                    $namaAyah, $noHpAyah, $namaIbu, $noHpIbu, $namaWali, $rombel
                ) {
                    $user = User::create([
                        'username'           => $nipd,
                        'password'           => $nipd,
                        'is_change_password' => false,
                        'is_active'          => true,
                    ]);
                    $user->assignRole('peserta_didik');

                    $pd = PesertaDidik::create([
                        'user_id'         => $user->id,
                        'nipd'            => $nipd,
                        'nisn'            => $nisn !== '' ? $nisn : null,
                        'nik'             => $nik !== '' ? $nik : null,
                        'nama_lengkap'    => $namaLengkap,
                        'jenis_kelamin'   => $jenisKelamin,
                        'tempat_lahir'    => $tempatLahir !== '' ? $tempatLahir : null,
                        'tanggal_lahir'   => $tanggalLahir,
                        'agama'           => $agama !== '' ? $agama : null,
                        'no_hp'           => $noHp !== '' ? $noHp : null,
                        'status_akademik' => 'aktif',
                    ]);

                    PesertaDidikAlamat::create([
                        'peserta_didik_id' => $pd->id,
                        'alamat'           => $alamat !== '' ? $alamat : null,
                        'rt'               => $rt !== '' ? $rt : null,
                        'rw'               => $rw !== '' ? $rw : null,
                        'dusun'            => $dusun !== '' ? $dusun : null,
                        'kelurahan'        => $kelurahan !== '' ? $kelurahan : null,
                        'kecamatan'        => $kecamatan !== '' ? $kecamatan : null,
                        'kode_pos'         => $kodePos !== '' ? $kodePos : null,
                    ]);

                    if ($namaAyah !== '') {
                        PesertaDidikOrtu::create([
                            'peserta_didik_id' => $pd->id,
                            'jenis'            => 'ayah',
                            'nama'             => $namaAyah,
                            'no_hp'            => $noHpAyah !== '' ? $noHpAyah : null,
                        ]);
                    }
                    if ($namaIbu !== '') {
                        PesertaDidikOrtu::create([
                            'peserta_didik_id' => $pd->id,
                            'jenis'            => 'ibu',
                            'nama'             => $namaIbu,
                            'no_hp'            => $noHpIbu !== '' ? $noHpIbu : null,
                        ]);
                    }
                    if ($namaWali !== '') {
                        PesertaDidikOrtu::create([
                            'peserta_didik_id' => $pd->id,
                            'jenis'            => 'wali',
                            'nama'             => $namaWali,
                        ]);
                    }

                    PesertaDidikRombel::create([
                        'peserta_didik_id' => $pd->id,
                        'rombel_id'        => $rombel->id,
                    ]);
                });

                $results[] = [
                    'row'    => $rowIndex,
                    'status' => 'berhasil',
                    'nipd'   => $nipd,
                    'nama'   => $namaLengkap,
                    'rombel' => $rombel->nama,
                    'alasan' => null,
                ];
            } catch (\Throwable $th) {
                $results[] = $this->fail($rowIndex, $nipd, $namaLengkap, 'Error sistem: ' . $th->getMessage());
            }

            $rowIndex++;
        }

        return $results;
    }

    private function fail(int $row, string $nipd, string $nama, string $alasan): array
    {
        return [
            'row'    => $row,
            'status' => 'gagal',
            'nipd'   => $nipd,
            'nama'   => $nama,
            'rombel' => '—',
            'alasan' => $alasan,
        ];
    }
}
