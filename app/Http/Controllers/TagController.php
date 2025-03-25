<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
=======
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98

class TagController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        try {
            $tags = Tag::withCount('places')->get();
            return response()->json($tags);
        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching tags'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:tags'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $tag = Tag::create($request->all());
            Log::info('Tag created successfully: ' . $tag->id);
            return response()->json($tag, 201);

        } catch (\Exception $e) {
            Log::error('Error creating tag: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating tag'], 500);
        }
    }

    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting tag: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting tag'], 500);
        }
    }

    public function getPlacesByTag($tag_id)
    {
        try {
            $tag = Tag::with('places')->findOrFail($tag_id);
            return response()->json($tag->places);
        } catch (\Exception $e) {
            Log::error('Error fetching places by tag: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching places'], 500);
        }
=======
        $tags = Tag::all();
        return response()->json($tags);
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
    }
} 