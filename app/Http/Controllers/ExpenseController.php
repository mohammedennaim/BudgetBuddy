<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())
                          ->with('tags')
                          ->orderBy('date', 'desc')
                          ->get();
        
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $expense = Expense::create([
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'user_id' => Auth::id()
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $tagIds = $request->tags;
            $validTags = Tag::whereIn('id', $tagIds)
                            ->where('user_id', Auth::id())
                            ->pluck('id')
                            ->toArray();
            $expense->tags()->sync($validTags);
        }
        return response()->json($expense->load('tags'), 201);
    }

    public function show($id)
    {
        $expense = Expense::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->with('tags')
                          ->first();
        
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->first();
        
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $request->validate([
            'amount' => 'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        if ($request->has('amount')) {
            $expense->amount = $request->amount;
        }
        
        if ($request->has('description')) {
            $expense->description = $request->description;
        }
        
        if ($request->has('date')) {
            $expense->date = $request->date;
        }
        
        $expense->save();
        if ($request->has('tags')) {
            $tagIds = $request->tags;
            $validTags = Tag::whereIn('id', $tagIds)
                            ->where('user_id', Auth::id())
                            ->pluck('id')
                            ->toArray();
            $expense->tags()->sync($validTags);
        }

        return response()->json($expense->load('tags'));
    }

    public function destroy($id)
    {
        $expense = Expense::where('id', $id)
                         ->where('user_id', Auth::id())
                         ->first();
        
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();
        return response()->json(['message' => 'Expense deleted successfully']);
    }

    public function attachTags(Request $request, $id)
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $expense = Expense::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->first();
                         
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }
        
        $tagIds = $request->tags;
        $validTags = Tag::whereIn('id', $tagIds)
                        ->where('user_id', Auth::id())
                        ->pluck('id')
                        ->toArray();

        if (count($validTags) !== count($tagIds)) {
            return response()->json(['message' => 'One or more tags are invalid or do not belong to you'], 400);
        }

        $expense->tags()->sync($validTags);
        return response()->json([
            'message' => 'Tags attached successfully',
            'expense' => $expense->load('tags')
        ]);
    }
}