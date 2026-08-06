<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'barista')->latest()->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|max:20',
            'address' => 'nullable|string',
            'pin' => 'nullable|digits:6',
            'joined_at' => 'nullable|date',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'barista';
        $validated['is_active'] = true;

        $user = User::create($validated);
        ActivityLog::log('create_employee', "Menambah pegawai: {$user->name}", $user);

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'nullable|max:20',
            'address' => 'nullable|string',
            'pin' => 'nullable|digits:6',
            'joined_at' => 'nullable|date',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        $employee->update($validated);
        ActivityLog::log('edit_employee', "Mengedit pegawai: {$employee->name}", $employee);

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil diupdate!');
    }

    public function destroy(User $employee)
    {
        ActivityLog::log('delete_employee', "Menghapus pegawai: {$employee->name}", $employee);
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil dihapus!');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status pegawai diubah!');
    }

    public function activityLog(User $user)
    {
        $logs = ActivityLog::where('user_id', $user->id)->latest('created_at')->paginate(20);
        return view('admin.employees.activity', compact('user', 'logs'));
    }
}
