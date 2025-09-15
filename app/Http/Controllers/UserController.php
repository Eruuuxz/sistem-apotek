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
        // Hanya tampilkan user dengan role 'kasir'
        $users = User::where('role', 'kasir')->paginate(10);
        return view('managementuser.index', compact('users'));
    }

    public function create()
    {
        $cabangs = Cabang::all(); // Ambil semua cabang
        return view('managementuser.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'cabang_id' => 'nullable|exists:cabang,id', // Validasi cabang_id
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir', // Default role adalah kasir
            'cabang_id' => $request->cabang_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Kasir berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Pastikan hanya admin yang bisa mengedit kasir
        if ($user->role !== 'kasir') {
            abort(403, 'Anda tidak diizinkan mengedit pengguna dengan peran ini.');
        }
        $cabangs = Cabang::all();
        return view('managementuser.edit', compact('user', 'cabangs'));
    }

    public function update(Request $request, User $user)
    {
        // Pastikan hanya admin yang bisa mengupdate kasir
        if ($user->role !== 'kasir') {
            abort(403, 'Anda tidak diizinkan mengupdate pengguna dengan peran ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed', // Password opsional saat update
            'cabang_id' => 'nullable|exists:cabang,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->cabang_id = $request->cabang_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Kasir berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Pastikan hanya admin yang bisa menghapus kasir
        if ($user->role !== 'kasir') {
            abort(403, 'Anda tidak diizinkan menghapus pengguna dengan peran ini.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Kasir berhasil dihapus.');
    }
}
