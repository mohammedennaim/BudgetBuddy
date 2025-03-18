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
            'members' => 'required|array',  // Renamed from user_id to members for clarity
            'users.*' => 'exists:users,id', // Validate each member
            'devise_id' => 'required|exists:devises,id',
        ]);

        // Create the group with the authenticated user as owner
        $depensesGroupe = depensesGroupe::create([
            'name' => $request->name, // The owner is the current user
            'devise_id' => $request->devise_id
        ]);
        
        // If you need to associate multiple users with this group,
        // you'll need a many-to-many relationship and a pivot table
        if ($request->has('members') && !empty($request->members)) {
            // Make sure your depensesGroupe model has a members() method
            // that defines a belongsToMany relationship with User model
            $depensesGroupe->members()->sync($request->members);
        }
        
        return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(201)
            ->header('message', 'depensesGroupe created successfully');
    }
    public function show($id)
    {
        $depensesGroupe = depensesGroupe::where('id', $id)->first();
        
        if (!$depensesGroupe) {
            return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(404)
            ->header('message', 'depensesGroupe not found');
        }
        
        return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(200)
            ->header('message', 'depensesGroupe retrieved successfully');
    }

    public function destroy($id)
    {
        $depensesGroupe = depensesGroupe::where('id', $id)->first();
        
        if (!$depensesGroupe) {
            return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(404)
            ->header('message', 'depensesGroupe not found');
        }
        
        $depensesGroupe->delete();
        
        return new depensesGroupeResource($depensesGroupe)->response()
            ->setStatusCode(200)
            ->header('message', 'depensesGroupe deleted successfully');
    }
}
