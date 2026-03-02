<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Membership;

class ExpenseController extends Controller
{
    public function store(Request $request, $colocationId)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);


        $membership = Membership::where('user_id', auth()->id())
            ->where('colocation_id', $colocationId)
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category_id' => $request->category_id,
            'colocation_id' => $colocationId,
            'membership_id' => $membership->id,
        ]);


        return redirect()->route('colocations.show', $colocationId)
                         ->with('success', 'Dépense ajoutée avec succès ! ✅');
    }
}
