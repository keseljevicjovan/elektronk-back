<?php

namespace App\Http\Controllers\Api\Image;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\{JsonResponse, Request};

class ImageController extends Controller
{
    /**
     * List all images with pagination
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 12);

        $images = Image::select('id', 'title', 'description', 'url', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($images);
    }

    /**
     * Show a single image
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $image = Image::select('id', 'title', 'description', 'url', 'created_at')
            ->findOrFail($id);

        return response()->json(['data' => $image]);
    }
}
