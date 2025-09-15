<?php

namespace App\Http\Controllers;

use App\Models\MedicalAction;
use Illuminate\Http\Request;

class MedicalActionController extends Controller
{
    public function index()
    {
        $medicalActions = MedicalAction::latest()->paginate(10);
        return view('medical_actions.index', compact('medicalActions'));
    }

    public function create()
    {
        return view('medical_actions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255|unique:medical_actions,nama_tindakan',
            'biaya_dasar' => 'required|numeric|min:0',
        ]);

        MedicalAction::create($request->all());
        return redirect()->route('medical_actions.index')->with('success', 'Tindakan medis berhasil ditambahkan.');
    }

    public function edit(MedicalAction $medicalAction)
    {
        return view('medical_actions.edit', compact('medicalAction'));
    }

    public function update(Request $request, MedicalAction $medicalAction)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255|unique:medical_actions,nama_tindakan,' . $medicalAction->id,
            'biaya_dasar' => 'required|numeric|min:0',
        ]);

        $medicalAction->update($request->all());
        return redirect()->route('medical_actions.index')->with('success', 'Tindakan medis berhasil diperbarui.');
    }

    public function destroy(MedicalAction $medicalAction)
    {
        $medicalAction->delete();
        return redirect()->route('medical_actions.index')->with('success', 'Tindakan medis berhasil dihapus.');
    }
}