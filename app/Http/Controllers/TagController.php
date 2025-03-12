<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())->get();
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'color' => $request->color,
            'user_id' => Auth::id(),
        ]);

        return response()->json($tag, 201);
    }


    public function show($id)
    {
        $tag = Tag::where('id', $id)
                  ->where('user_id', Auth::id())
                  ->first();
        
        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json($tag);
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

        $tag->update([
            'name' => $request->name ?? $tag->name,
            'color' => $request->color ?? $tag->color,
        ]);

        return response()->json($tag);
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

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}