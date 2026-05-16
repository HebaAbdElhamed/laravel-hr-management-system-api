<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{

    public function index()
    {
        $attendances =  Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->withCount('user')
            ->get();

        $totalEmployees = User::where('role', 'employee')->count();

        // 3. نرجع الرد في شكل Object منظم للفرونت إند
        return response()->json([
            'total_employee_count' => $totalEmployees,
            'data' => $attendances
        ]);
    }



    public function checkIn(Request $request, AttendanceService $service)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();

        $workStart = env('WORK_START_TIME', '09:00:00');
        $workEnd = env('WORK_END_TIME', '17:00:00');

        $startTime = Carbon::createFromTimeString($workStart);
        $endTime = Carbon::createFromTimeString($workEnd);
        $allowedFrom = $startTime->copy()->subMinutes(30);


        if ($now->lessThan($allowedFrom)) {
            return response()->json([
                'message' => 'Too early! You can check in starting from ' . $allowedFrom->format('g:i A')
            ], 403);
        }

        if ($now->greaterThan($endTime)) {
            return response()->json([
                'message' => 'Shift ended! You cannot check in after ' . $endTime->format('g:i A')
            ], 403);
        }

        if (Attendance::where('user_id', $user->id)->where('date', $today)->exists()) {
            return response()->json(['message' => 'You already checked in today!'], 400);
        }

        $distance = $service->calculateDistance(
            $request->lat,
            $request->lng,
            env('OFFICE_LAT'),
            env('OFFICE_LNG')
        );

        if ($distance > env('OFFICE_RADIUS', 100)) {
            return response()->json(['message' => 'You are outside the office range!'], 403);
        }

        $lateMinutes = 0;
        if ($now->toTimeString() > $workStart) {
            // Force absolute value to be 100% sure it can never be negative
            $lateMinutes = abs($now->diffInMinutes($startTime));
        }
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now->toTimeString(),
            'late_minutes' => $lateMinutes,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => $lateMinutes > 0 ? 'late' : 'on-time'
        ]);

        return response()->json(['message' => 'Checked in successfully!', 'data' => $attendance]);
    }



    public function checkOut(Request $request, AttendanceService $service)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();


        if (!$attendance) {
            return response()->json(['message' => 'No check-in record found for today.'], 404);
        }
        if ($attendance->check_out) {
            return response()->json(['message' => 'You have already checked out for today.'], 400);
        }
        $distance = $service->calculateDistance(
            $request->lat,
            $request->lng,
            env('OFFICE_LAT'),
            env('OFFICE_LNG')
        );

        if ($distance > env('OFFICE_RADIUS', 100)) {
            return response()->json([
                'message' => 'You must be within office range to check out!'
            ], 403);
        }

        $attendance->update(['check_out' => Carbon::now()->toTimeString()]);

        return response()->json([
            'message' => 'Checked out successfully!',
            'data' => $attendance
        ]);
    }


    public function myHistory()
    {
        return Attendance::where('user_id', Auth::id())
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();
    }
}