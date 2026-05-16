<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return User::with('department')
            ->where('role', 'employee')
            ->latest()
            ->get();
    }
    public function show($id)
    {
        return User::with('department')->findOrFail($id);
    }
    public function update(Request $request, $id, EmployeeService $service)
    {
        $employee = User::findOrFail($id);
        $fields_Validation = $request->validate([
            'name' => 'sometimes|string',
            'email' => "sometimes|email|unique:users,email,$id",
            'department_id' => 'sometimes|exists:departments,id',
            'job_title' => 'sometimes|string',
            'salary' => 'sometimes|numeric',
            'status' => 'sometimes|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'join_date' => 'sometimes|date',
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'password' => 'sometimes|nullable|string|min:8',
        ]);
        if ($request->hasFile('image')) {
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }
            $fields_Validation['image'] = $request->file('image')->store('employees', 'public');
        }
        if (!empty($fields_Validation['password'])) {
            $fields_Validation['password'] = Hash::make($fields_Validation['password']);
        } else {
            unset($fields_Validation['password']);
        }
        $employee->update($fields_Validation);
        if ($request->has('department_id')) {
            $department = Department::find($fields_Validation['department_id']);
            $employee->code = $service->generateCode($department, $employee->id);
            $employee->save();
        }
        return response()->json(['message' => 'Updated successfully', 'user' => $employee]);
    }
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'Employee deleted']);
    }
    public function store(Request $request, EmployeeService $service)
    {
        $fields_Validation = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'join_date' => 'required|date',
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'password' => ['required', Password::min(8)->letters()->numbers()->symbols()]
        ]);
        if ($request->hasFile('image')) {
            $fields_Validation['image'] = $request->file('image')->store('employees', 'public');
        }
        $user = User::create([
            'name' => $fields_Validation['name'],
            'email' => $fields_Validation['email'],
            'password' => Hash::make($fields_Validation['password']),
            'role' => 'employee',
            'job_title' => $fields_Validation['job_title'],
            'salary' => $fields_Validation['salary'],
            'status' => $fields_Validation['status'],
            'department_id' => $fields_Validation['department_id'],
            'image' => $fields_Validation['image'] ?? null,
            'join_date' => $fields_Validation['join_date'],
            'phone' => $fields_Validation['phone'],
            'address' => $fields_Validation['address'],
        ]);
        $department = Department::find($fields_Validation['department_id']);
        $user->code = $service->generateCode($department, $user->id);
        $user->save();
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
}
