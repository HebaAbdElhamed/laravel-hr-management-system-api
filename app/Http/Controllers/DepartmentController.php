<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::with('users')->withCount('users')->get();
    }

    public function show($id)
    {
        return Department::findOrFail($id);
    }

    public function store(Request $request,DepartmentService $service)
    {
        $request->validate([
            'name' => 'required|unique:departments'
        ]);

        return Department::create([
            'name' => $request->name,
            'code' => $service->generateCode($request->name)
        ]);
    }


    public function update(Request $request, $id , DepartmentService $service)
    {
        $department = Department::findOrFail($id);

        $department->update([
            'name' => $request->name,
            'code' => $service->generateCode($request->name)
        ]);

        return $department;
    }

    public function destroy($id)
    {
        return Department::destroy($id);
    }
}
