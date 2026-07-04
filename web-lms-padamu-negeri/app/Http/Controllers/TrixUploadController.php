<?php

namespace App\Http\Controllers;

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

        $path = $request->file('file')->store('materi-inline', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}
