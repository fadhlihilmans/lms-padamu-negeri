<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesertaDidikTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles, WithColumnFormatting
{
    /**
     * Kolom yang WAJIB diformat TEKS agar angka 0 di depan tidak hilang
     * (mis. NIPD `007`, RT `001`, No HP `0812…`) dan angka panjang tidak
     * berubah jadi notasi ilmiah (mis. NIK 16 digit → 3.3E+15).
     */
    private const KOLOM_TEKS = [
        'B', // NIPD
        'C', // NISN
        'D', // NIK
        'J', // No HP Peserta Didik
        'P', // RT
        'Q', // RW
        'U', // Kode Pos
        'W', // No HP Ayah
        'Y', // No HP Ibu
    ];

    /** Baris terakhir yang ikut diberi format teks (agar isian baru Admin tetap teks). */
    private const BARIS_TERFORMAT = 500;

    public function headings(): array
    {
        // Tanda (*) = kolom wajib diisi.
        return [
            'No', 'NIPD *', 'NISN', 'NIK', 'Nama Lengkap *',
            'Jenis Kelamin *', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'No HP Peserta Didik',
            'Email', 'Wilayah *', 'Paket *', 'Tingkat *', 'Alamat',
            'RT', 'RW', 'Dusun', 'Kelurahan', 'Kecamatan',
            'Kode Pos', 'Nama Ayah', 'No HP Ayah', 'Nama Ibu', 'No HP Ibu',
            'Nama Wali',
        ];
    }

    public function array(): array
    {
        // Baris contoh agar Admin tahu format yang diharapkan
        return [
            [
                1, '1718', '1234567890', '3300000000000001', 'Ahmad Fauzi',
                'L', 'Jakarta', '2000-01-15', 'Islam', '08123456789',
                '', 'Botolambat', 'C', '10', 'Jl. Contoh No. 1',
                '001', '002', '', 'Kel. Contoh', 'Kec. Contoh',
                '51200', 'Nama Ayah', '08111111111', 'Nama Ibu', '08222222222',
                '',
            ],
        ];
    }

    /** Format kolom pada data yang ditulis. */
    public function columnFormats(): array
    {
        return array_fill_keys(self::KOLOM_TEKS, NumberFormat::FORMAT_TEXT);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 12, 'C' => 14, 'D' => 20, 'E' => 25,
            'F' => 14, 'G' => 18, 'H' => 16, 'I' => 12, 'J' => 18,
            'K' => 22, 'L' => 15, 'M' => 10, 'N' => 10, 'O' => 30,
            'P' => 6,  'Q' => 6,  'R' => 15, 'S' => 18, 'T' => 18,
            'U' => 10, 'V' => 22, 'W' => 16, 'X' => 22, 'Y' => 16,
            'Z' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Terapkan format TEKS ke rentang baris kosong juga, supaya ketika Admin
        // mengetik `007` di baris baru, Excel tetap menyimpannya sebagai teks.
        foreach (self::KOLOM_TEKS as $kolom) {
            $sheet->getStyle($kolom . '2:' . $kolom . self::BARIS_TERFORMAT)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '3c50e0']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
