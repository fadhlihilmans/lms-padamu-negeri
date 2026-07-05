<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;

/**
 * Kompresi gambar terpusat: resize dimensi maks → encode WebP hingga mendekati
 * target ukuran (dari setting `kompres_target_kb`) → simpan ke disk.
 *
 * Prinsip aman (Fase Revisi Compress Image):
 * - Hanya gambar raster (jpeg/png/webp/bmp) yang dikompres; file lain di-store apa adanya.
 * - Bila kompresi gagal karena alasan apa pun → fallback simpan file original
 *   (upload TIDAK pernah gagal gara-gara kompresi) + dicatat ke error_log.
 */
class ImageService
{
    /** Dimensi maksimum (px) sisi terpanjang; gambar lebih besar diperkecil proporsional. */
    private const MAX_DIMENSION = 1600;

    /** Langkah kualitas WebP dari tinggi ke rendah untuk mengejar target ukuran. */
    private const QUALITY_STEPS = [82, 74, 66, 58, 50, 42];

    /** MIME gambar raster yang aman dikompres (SVG/GIF sengaja dikecualikan). */
    private const COMPRESSIBLE = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/bmp'];

    /**
     * Simpan file upload: kompres bila gambar, selain itu store apa adanya.
     * Mengembalikan path relatif pada disk (mis. "materi/xxxx.webp").
     */
    public function store($file, string $dir, string $disk = 'public'): string
    {
        if (! $this->isCompressibleImage($file)) {
            return $file->store($dir, $disk);
        }

        return $this->compress($file, $dir, $disk);
    }

    /**
     * Kompres gambar ke WebP dan simpan. Menganggap $file adalah gambar raster.
     * Fallback ke file original bila terjadi error.
     */
    public function compress($file, string $dir, string $disk = 'public'): string
    {
        try {
            $targetBytes = $this->targetBytes();

            $image = (new ImageManager($this->driver()))->decodePath($file->getRealPath());

            // Perkecil bila melebihi dimensi maksimum (proporsional, tidak memperbesar).
            $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);

            // Turunkan kualitas bertahap sampai <= target (atau kualitas terendah).
            // strip: true → buang metadata EXIF/ICC untuk hemat ukuran.
            $binary = null;
            foreach (self::QUALITY_STEPS as $quality) {
                $binary = (string) $image->encode(new WebpEncoder(quality: $quality, strip: true));
                if (strlen($binary) <= $targetBytes) {
                    break;
                }
            }

            $path = trim($dir, '/') . '/' . Str::random(40) . '.webp';
            Storage::disk($disk)->put($path, $binary);

            return $path;
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Kompres Gambar', $th, [
                'original' => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
            ]);

            // Jaring pengaman: simpan file original apa adanya.
            return $file->store($dir, $disk);
        }
    }

    /** Apakah file merupakan gambar raster yang aman dikompres. */
    public function isCompressibleImage($file): bool
    {
        if (! is_object($file) || ! method_exists($file, 'getMimeType')) {
            return false;
        }

        try {
            return in_array($file->getMimeType(), self::COMPRESSIBLE, true);
        } catch (\Throwable) {
            return false;
        }
    }

    /** Target ukuran hasil kompres dalam byte (dari setting, minimal 20 KB). */
    private function targetBytes(): int
    {
        $kb = (int) app(SettingService::class)->get('kompres_target_kb', 300);

        return max(20, $kb) * 1024;
    }

    /** Pilih driver: Imagick bila tersedia, jika tidak GD. */
    private function driver(): DriverInterface
    {
        return extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
    }
}
