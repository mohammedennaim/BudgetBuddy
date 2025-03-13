<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TagResource;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())->get();
        return TagResource::collection($tags)
            ->response()
            ->setStatusCode(200)
            ->header('message', 'Tags retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'color' => $request->color ?? '#000000',
            'user_id' => Auth::id(),
        ]);

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(201)
            ->header('message', 'Tag created successfully');
    }

    public function show($id)
    {
        $tag = Tag::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(200)
            ->header('message', 'Tag retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|nullable|string|max:7',
        ]);

        // Only update fields that were provided
        if ($request->has('name')) {
            $tag->name = $request->name;
        }
        
        if ($request->has('color')) {
            $tag->color = $request->color ?? '#000000';
        }
        
        $tag->save();

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(200)
            ->header('message', 'Tag updated successfully');
    }

    public function destroy($id)
    {
        $tag = Tag::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }
}