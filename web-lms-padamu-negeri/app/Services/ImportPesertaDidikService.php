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

            $nipd        = $this->str($row[1] ?? null);
            $nisn        = $this->str($row[2] ?? null);
            $nik         = $this->str($row[3] ?? null);
            $namaLengkap = $this->str($row[4] ?? null);
            $jenisKelamin = strtoupper($this->str($row[5] ?? null));
            $tempatLahir = $this->str($row[6] ?? null);
            $tanggalLahirRaw = $row[7] ?? null;
            $agama       = $this->str($row[8] ?? null);
            $noHp        = $this->str($row[9] ?? null);
            // kolom K (index 10) = Email — tidak disimpan di DB
            $namaWilayah = $this->str($row[11] ?? null);
            $namaPaket   = $this->str($row[12] ?? null);
            $namaTingkat = $this->str($row[13] ?? null);
            $alamat      = $this->str($row[14] ?? null);
            $rt          = $this->str($row[15] ?? null);
            $rw          = $this->str($row[16] ?? null);
            $dusun       = $this->str($row[17] ?? null);
            $kelurahan   = $this->str($row[18] ?? null);
            $kecamatan   = $this->str($row[19] ?? null);
            $kodePos     = $this->str($row[20] ?? null);
            $namaAyah    = $this->str($row[21] ?? null);
            $noHpAyah    = $this->str($row[22] ?? null);
            $namaIbu     = $this->str($row[23] ?? null);
            $noHpIbu     = $this->str($row[24] ?? null);
            $namaWali    = $this->str($row[25] ?? null);

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
                ->where('tahun_ajaran', \App\Models\PeriodeAjaran::find($periodeId)?->tahun_ajaran)
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

    /**
     * Ubah nilai sel Excel menjadi string dengan aman.
     *
     * PhpSpreadsheet mengembalikan sel angka sebagai int/float. Casting float
     * langsung ke string menghasilkan NOTASI ILMIAH untuk angka panjang —
     * mis. NIK 16 digit `3300000000000001` menjadi `3.3E+15`. Karena itu float
     * diformat sebagai bilangan bulat penuh.
     *
     * Catatan: angka 0 di depan (mis. `007`) hanya bertahan bila selnya bertipe
     * TEKS di file Excel — itulah sebabnya template kini memformat kolom
     * NIPD/NISN/NIK/HP/RT/RW/Kode Pos sebagai teks.
     */
    private function str(mixed $v): string
    {
        if ($v === null) {
            return '';
        }

        if (is_float($v)) {
            return trim(number_format($v, 0, '.', ''));
        }

        return trim((string) $v);
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
