<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employee = new Employee;
        $employee->name = $request->name;
        $employee->position = $request->position;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $employee->image = $imagePath;
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employee->name = $request->name;
        $employee->position = $request->position;

        if ($request->hasFile('image')) {
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $employee->image = $imagePath;
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->image) {
            Storage::disk('public')->delete($employee->image);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
