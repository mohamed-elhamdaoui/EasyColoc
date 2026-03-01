<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;

class ColocationController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $memberships = $user->memberships()->with('colocation')->get();


        return view('colocations.index', [
            'memberships' => $memberships
        ]);
    }


    public function show($id)
    {

        $colocation = Colocation::findOrFail($id);

        $currentUserMembership = $colocation->memberships()
            ->where('user_id', auth()->id())
            ->first();

        $members = $colocation->memberships()->with('user')->get();

        return view('colocations.show', [
            'colocation' => $colocation,
            'currentUserMembership' => $currentUserMembership, 
            'members' => $members
        ]);
    }

    public function store(Request $request)
    {
        $hasActiveColocation = Membership::where('user_id', auth()->id())
            ->where('status', 'ACTIVE')
            ->exists();

        if ($hasActiveColocation) {
            return redirect()->back()->with('error', 'vous etes deja dans une colocation activeee');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $colocation = Colocation::create([
            'name' => $request->name,
        ]);

        Membership::create([
            'user_id' => auth()->id(),
            'colocation_id' => $colocation->id,
            'role' => 'OWNER',
        ]);

        return redirect()->route('colocations.index')->with('success', 'Colocation creee');
    }
}
