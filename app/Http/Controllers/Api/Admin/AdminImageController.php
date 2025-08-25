<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\{JsonResponse, Response, Request};
use Illuminate\Support\Facades\Storage;

class AdminImageController extends Controller
{
    /**
     * List all images with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);

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

    /**
     * Store a new image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|image|max:4096'
        ]);

        $path = $request->file('file')->store('images', 'public');

        $image = Image::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url'         => "/storage/$path"
        ]);

        return response()->json(['data' => $image], 201);
    }

    /**
     * Update an existing image
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $image = Image::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'file'        => 'sometimes|image|max:4096'
        ]);

        if ($request->hasFile('file')) {
            if ($image->url) {
                $oldPath = str_replace('/storage/', '', $image->url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('file')->store('images', 'public');
            $validated['url'] = "/storage/$path";
        }

        $image->update($validated);

        return response()->json(['data' => $image]);
    }

    /**
     * Delete an image
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $image = Image::findOrFail($id);

        if ($image->url) {
            $oldPath = str_replace('/storage/', '', $image->url);
            Storage::disk('public')->delete($oldPath);
        }

        $image->delete();

        return response()->noContent();
    }
}
