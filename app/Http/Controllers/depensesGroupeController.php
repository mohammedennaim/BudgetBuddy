<?php

namespace App\Http\Controllers;

use App\Http\Resources\depensesGroupeResource;
use App\Models\depensesGroupe;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class depensesGroupeController extends Controller
{
    /**
     * Display a listing of the expense groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depensesGroupe = depensesGroupe::all();
        
        return response()->json($depensesGroupe, 200, [
            'message' => 'depensesGroupe retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'devise_id' => 'required|exists:devises,id',
        ]);

        $depensesGroupe = depensesGroupe::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'devise_id' => $request->devise_id
        ]);
        
        return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(201)
            ->header('message', 'depensesGroupe created successfully');
    }
}
