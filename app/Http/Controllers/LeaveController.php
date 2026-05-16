<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    // 1. Employee: View their own leaves
    public function myLeaves()
    {
        $leaves = Leave::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['data' => $leaves]);
    }

    // 2. Employee: Submit a leave request
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:casual,annual',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);


        $exists = Leave::where('user_id', $user->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'You already have a leave request during these dates.'], 400);
        }
        $daysRequested = $start->diffInDays($end) + 1;

        $balanceField = $request->type . '_balance';

        if ($user->$balanceField < $daysRequested) {
            return response()->json(['message' => 'Insufficient leave balance.'], 400);
        }

        $status = 'pending';
        if ($request->type === 'casual') {
            // Use decrement to subtract the days immediately
            $user->decrement($balanceField, $daysRequested);
            $status = 'approved';
        }

        $leave = Leave::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'reason' => $request->reason,
            'status' => $status,
        ]);

        return response()->json([
            'message' => $status === 'approved' ? 'Casual leave approved automatically.' : 'Leave request submitted successfully.',
            'data' => $leave
        ], 201);
    }

    // 3. Employee: Cancel pending request
    public function cancel($id)
    {
        $leave = Leave::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $leave->delete();
        return response()->json(['message' => 'Leave request cancelled successfully.']);
    }

    // 4. Admin: View all requests
    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $leaves = Leave::with('user:id,name,casual_balance,annual_balance')->latest()->get();
        return response()->json(['data' => $leaves]);
    }

    // 5. Admin: Approve or Reject
    public function adminDecision(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate(['status' => 'required|in:approved,rejected']);

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 400);
        }

        if ($request->status === 'approved') {
            $employee = User::find($leave->user_id); // Ensure we have the User model instance

            if ($leave->type === 'annual') {
                if ($employee->annual_balance < $leave->days_requested) {
                    return response()->json(['message' => 'Employee does not have enough annual balance.'], 400);
                }
                $employee->decrement('annual_balance', $leave->days_requested);
            }
        }

        $leave->update(['status' => $request->status]);

        return response()->json(['message' => 'Leave status updated to ' . $request->status]);
    }

    public function getBalances()
    {
        return response()->json([
            'casual_balance' => (int) Auth::user()->casual_balance, // تحويل لـ Integer للتأكيد
            'annual_balance' => (int) Auth::user()->annual_balance,
        ]);
    }
}