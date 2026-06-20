<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesertaDidikTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function headings(): array
    {
        return [
            'No', 'NIPD', 'NISN', 'NIK', 'Nama Lengkap',
            'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'No HP Peserta Didik',
            'Email', 'Wilayah', 'Paket', 'Tingkat', 'Alamat',
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
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '3c50e0']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
