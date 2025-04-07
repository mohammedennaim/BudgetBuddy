<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupeResource;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{

    public function index()
    {
        $groupe = Groupe::all();

        return response()->json($groupe, 200, [
            'message' => 'depensesGroupe retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array',
            'users.*' => 'exists:users,id',
            'devise' => 'required|string|max:255',
        ]);

        $members = json_encode($request->members);

        $groupe = Groupe::create([
            'name' => $request->name,
            'members' => $members,
            'devise' => $request->devise,
        ]);

        if (!empty($request->members)) {
            $groupe->members()->attach($request->members);
        }

        $groupe->load('members');

        return new GroupeResource($groupe)->response()
            ->setStatusCode(201)
            ->header('message', 'depensesGroupe created successfully');
    }
    public function show($id)
    {
        $groupe = Groupe::where('id', $id)->first();

        if (!$groupe) {
            return new GroupeResource($groupe)->response()
                ->setStatusCode(404)
                ->header('message', 'depensesGroupe not found');
        }

        return new GroupeResource($groupe)->response()
            ->setStatusCode(200)
            ->header('message', 'depensesGroupe retrieved successfully');
    }

    public function destroy($id)
    {
        $groupe = Groupe::where('id', $id)->first();

        if (!$groupe) {
            return new GroupeResource($groupe)->response()
                ->setStatusCode(404)
                ->header('message', 'depensesGroupe not found');
        }

        $groupe->delete();

        return new GroupeResource($groupe)->response()
            ->setStatusCode(200)
            ->header('message', 'depensesGroupe deleted successfully');
    }

    // public function attachExpenses(Request $request, $id)
    // {
    //     $groupe = Groupe::where('id', $id)->first();

    //     if (!$groupe) {
    //         return new GroupeResource($groupe)->response()
    //             ->setStatusCode(404)
    //             ->header('message', 'depensesGroupe not found');
    //     }

    //     $groupe->expenses()->attach($request->expenses);

    //     return new GroupeResource($groupe)->response()
    //         ->setStatusCode(200)
    //         ->header('message', 'depensesGroupe expenses attached successfully');
    // }

    // public function detachExpense(Request $request, $id, $expenseId)
    // {
    //     $groupe = Groupe::where('id', $id)->first();

    //     if (!$groupe) {
    //         return new GroupeResource($groupe)->response()
    //             ->setStatusCode(404)
    //             ->header('message', 'depensesGroupe not found');
    //     }

    //     $groupe->expenses()->detach($expenseId);

    //     return new GroupeResource($groupe)->response()
    //         ->setStatusCode(200)
    //         ->header('message', 'depensesGroupe expense detached successfully');
    // }

    public function addExpense(Request $request, $id)
    {
        $request->validate([
            'amountTotal' => 'required|numeric',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'payers' => 'required|array',
            'payers.*.user_id' => 'required|exists:users,id',
            'payers.*.amount' => 'required|numeric|min:0',
            'split_type' => 'required|in:equal,custom',
            'participants' => 'required|array',
            'participants.*.user_id' => 'required|exists:users,id',
            'participants.*.share' => 'required_if:split_type,custom|numeric|min:0',
        ]);

        $groupe = Groupe::where('id', $id)->first();
        if (!$groupe) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $groupMemberIds = $groupe->members()->pluck('users.id')->toArray();
        $payerIds = collect($request->payers)->pluck('user_id')->toArray();
        $participantIds = collect($request->participants)->pluck('user_id')->toArray();

        $nonMemberPayers = array_diff($payerIds, $groupMemberIds);
        $nonMemberParticipants = array_diff($participantIds, $groupMemberIds);

        if (!empty($nonMemberPayers) || !empty($nonMemberParticipants)) {
            return response()->json([
                'message' => 'Some users are not members of this group',
                'non_member_payers' => $nonMemberPayers,
                'non_member_participants' => $nonMemberParticipants
            ], 400);
        }

        $expense = new Expense([
            'amountTotal' => $request->amountTotal,
            'description' => $request->description,
            'date' => $request->date,
            'user_id' => auth()->id(),
        ]);
        $expense->save();

        $groupe->expenses()->attach($expense->id);

        foreach ($request->payers as $payer) {
            $expense->payments()->create([
                'user_id' => $payer['user_id'],
                'amountPaid' => $payer['amountPaid'],
            ]);
        }

        $splitAmount = $request->amount / count($request->participants);
        if ($request->split_type === 'equal') {
            foreach ($request->participants as $participant) {
                $expense->splits()->create([
                    'user_id' => $participant['user_id'],
                    'amountRemaining' => 0,
                    'type' => 'equal',
                ]);
            }
        } elseif ($request->split_type !== 'equal') {
            foreach ($request->participants as $participant) {
                if ($participant['share'] > $splitAmount) {
                    $expense->splits()->create([
                        'user_id' => $participant['user_id'],
                        'amountRemaining' =>  - $participant['share'],
                        'type' => 'kitsal',
                    ]);
                }else{
                    $expense->splits()->create([
                        'user_id' => $participant['user_id'],
                        'amount' => $splitAmount + $participant['share'],
                        'type' => 'khas ikhales',
                    ]);
                }
            }
        }

        $expense->load(['payments.user', 'splits.user']);

        return response()->json([
            'message' => 'Expense added to group successfully',
            'expense' => $expense
        ], 201);
    }

    public function getExpenses($id)
    {
        $groupe = Groupe::where('id', $id)->first();

        if (!$groupe) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $expenses = $groupe->expenses()
            ->with(['payments.user', 'splits.user'])
            ->get();

        return response()->json([
            'message' => 'Group expenses retrieved successfully',
            'expenses' => $expenses
        ], 200);
    }

    public function getSettlements($id)
    {
        $groupe = Groupe::where('id', $id)->first();

        if (!$groupe) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $expenses = $groupe->expenses()
            ->with(['payments', 'splits'])
            ->get();

        $balances = [];
        $settlements = [];

        foreach ($expenses as $expense) {
            foreach ($expense->payments as $payment) {
                if (!isset($balances[$payment->user_id])) {
                    $balances[$payment->user_id] = 0;
                }
                $balances[$payment->user_id] += $payment->amount;
            }
            foreach ($expense->splits as $split) {
                if (!isset($balances[$split->user_id])) {
                    $balances[$split->user_id] = 0;
                }
                $balances[$split->user_id] -= $split->amount;
            }
        }

        $creditors = array_filter($balances, function ($balance) {
            return $balance > 0;
        });

        $debtors = array_filter($balances, function ($balance) {
            return $balance < 0;
        });

        foreach ($debtors as $debtorId => $debtorBalance) {
            $debtorBalance = abs($debtorBalance);
            foreach ($creditors as $creditorId => &$creditorBalance) {
                if ($debtorBalance <= 0)
                    break;
                if ($creditorBalance <= 0)
                    continue;

                $settleAmount = min($debtorBalance, $creditorBalance);

                $settlements[] = [
                    'from_user_id' => $debtorId,
                    'to_user_id' => $creditorId,
                    'amount' => $settleAmount,
                ];

                $debtorBalance -= $settleAmount;
                $creditorBalance -= $settleAmount;
            }
        }

        return response()->json([
            'message' => 'Group settlements calculated successfully',
            'balances' => $balances,
            'settlements' => $settlements
        ], 200);
    }

    public function detachExpense(Request $request, $id, $expenseId)
    {
        $groupe = Groupe::where('id', $id)->first();
        if (!$groupe) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $expense = Expense::find($expenseId);
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $isAttached = $groupe->expenses()->where('expense_id', $expenseId)->exists();
        if (!$isAttached) {
            return response()->json(['message' => 'This expense does not belong to the specified group'], 400);
        }

        $splitsDeleted = ExpenseSplit::where('expense_id', $expenseId)->delete();
        $groupe->expenses()->detach($expenseId);
        $expense->delete();

        return response()->json([
            'message' => 'Expense removed successfully',
            'details' => [
                'splits_deleted' => $splitsDeleted
            ]
        ], 200);
    }
}
