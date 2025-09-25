<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang; // Import model Cabang
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Import Rule

class UserController extends Controller
{
    public function index()
    {
        // Tampilkan user dengan role 'kasir' dan 'admin'
        $users = User::whereIn('role', ['kasir', 'admin'])->paginate(10);
        return view('admin.managementuser.index', compact('users'));
    }

    public function create()
    {
        $cabangs = Cabang::all(); // Ambil semua cabang
        return view('admin.managementuser.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:kasir,admin', // Validasi role
            'cabang_id' => 'nullable|exists:cabang,id', // Validasi cabang_id
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Ambil role dari request
            'cabang_id' => $request->cabang_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $cabangs = Cabang::all();
        return view('admin.managementuser.edit', compact('user', 'cabangs'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed', // Password opsional saat update
            'role' => 'required|in:kasir,admin',
            'cabang_id' => 'nullable|exists:cabang,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->cabang_id = $request->cabang_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}