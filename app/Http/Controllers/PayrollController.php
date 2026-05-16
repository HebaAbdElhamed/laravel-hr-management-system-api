<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        $employees = User::where('role', 'employee')->get();
        $count = 0;

        foreach ($employees as $employee) {
            // التأكد إننا مكررناش الحسبة لنفس الشهر
            $exists = Payroll::where('user_id', $employee->id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->exists();

            if (!$exists) {
                $this->calculatePayroll($employee, $request->month, $request->year);
                $count++;
            }
        }

        return response()->json(['message' => "Successfully generated payroll for $count employees."]);
    }

    private function calculatePayroll($user, $month, $year)
    {
        $basicSalary = $user->salary;
        $dayRate = $basicSalary / 30;
        $hourRate = $dayRate / 8;

        // 1. حساب التأخير (من سبرنت Attendance)
        $totalLateMinutes = Attendance::where('user_id', $user->id)
    ->whereMonth('date', $month)
    ->whereYear('date', $year)
    ->where('late_minutes', '>', 0) // أضف هذا الشرط لجلب التأخير الفعلي فقط
    ->sum('late_minutes');
        // حسبة الخصم: كل دقيقة تأخير = سعر الدقيقة (تقدر تعدل المعادلة دي)
        $lateDeduction = ($totalLateMinutes) * ($hourRate / 60);

        // 2. حساب الغياب (مثال مبسط)
        $absentDays = 0; // تقدر تربطها بجدول الحضور لو مفيش Record لليوم
        $absentDeduction = $absentDays * $dayRate;

        $totalDeductions = $lateDeduction + $absentDeduction;

        return Payroll::create([
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'basic_salary' => $basicSalary,
            'deductions' => $totalDeductions,
            'net_salary' => $basicSalary - $totalDeductions,
            'details' => [
                'late_minutes' => $totalLateMinutes,
                'late_deduction' => round($lateDeduction, 2),
                'absent_days' => $absentDays,
                'absent_deduction' => round($absentDeduction, 2),
            ],
            'status' => 'pending'
        ]);
    }


    // --- للموظف: عرض سجل مرتباته بالكامل ---
    public function myPayroll()
    {
        // بنجيب كل الشهور من الأحدث للأقدم
        $payrolls = Payroll::where('user_id', Auth::id())
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json(['data' => $payrolls]);
    }

    // --- للأدمن: عرض كل الموظفين لشهر معين ---
    public function index(Request $request)
    {
        // فلترة اختيارية بالشهر والسنة
        $payrolls = Payroll::with('user:id,name,job_title')
            ->when($request->month, fn($q) => $q->where('month', $request->month))
            ->when($request->year, fn($q) => $q->where('year', $request->year))
            ->latest()
            ->get();

        return response()->json(['data' => $payrolls]);
    }

    // --- للأدمن: تحويل الحالة لمدفوع ---
    public function markAsPaid($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update(['status' => 'paid']);

        return response()->json(['message' => 'Payroll status updated to Paid 💰']);
    }
}