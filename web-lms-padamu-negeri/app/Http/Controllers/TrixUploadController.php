<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TrixUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        // Gambar inline dikompres ke WebP; bila kompresi gagal, ImageService
        // otomatis fallback menyimpan file original (upload tidak pernah gagal).
        $path = app(ImageService::class)->store($request->file('file'), 'materi-inline', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}
