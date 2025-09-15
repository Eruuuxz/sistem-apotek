<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\MedicalAction;
use App\Models\Obat;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with('pelanggan')->latest()->paginate(10);
        return view('consultations.index', compact('consultations'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $medicalActions = MedicalAction::all();
        $obats = Obat::all(); // Untuk daftar obat yang bisa diresepkan
        return view('consultations.create', compact('pelanggans', 'medicalActions', 'obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'doctor_name' => 'required|string|max:255',
            'tanggal_konsultasi' => 'required|date',
            'biaya_konsultasi' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'medical_actions.*.id' => 'nullable|exists:medical_actions,id',
            'medical_actions.*.biaya_override' => 'nullable|numeric|min:0',
            'obats.*.id' => 'nullable|exists:obats,id',
            'obats.*.qty' => 'nullable|integer|min:1',
            'obats.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $consultation = Consultation::create([
                'pelanggan_id' => $request->pelanggan_id,
                'doctor_name' => $request->doctor_name,
                'tanggal_konsultasi' => $request->tanggal_konsultasi,
                'biaya_konsultasi' => $request->biaya_konsultasi,
                'catatan' => $request->catatan,
                'status' => 'pending',
            ]);

            $totalBiayaTindakan = 0;
            if ($request->has('medical_actions')) {
                foreach ($request->medical_actions as $actionData) {
                    if (isset($actionData['id'])) {
                        $medicalAction = MedicalAction::find($actionData['id']);
                        $biaya = $actionData['biaya_override'] ?? $medicalAction->biaya_dasar;
                        $consultation->medicalActions()->attach($medicalAction->id, ['biaya_tindakan_override' => $biaya]);
                        $totalBiayaTindakan += $biaya;
                    }
                }
            }

            $totalBiayaObat = 0;
            if ($request->has('obats')) {
                foreach ($request->obats as $obatData) {
                    if (isset($obatData['id']) && isset($obatData['qty']) && isset($obatData['harga_satuan'])) {
                        $obat = Obat::find($obatData['id']);
                        if ($obat->stok < $obatData['qty']) {
                            DB::rollBack();
                            return back()->withInput()->with('error', 'Stok obat ' . $obat->nama . ' tidak mencukupi.');
                        }
                        $subtotal = $obatData['qty'] * $obatData['harga_satuan'];
                        $consultation->obats()->attach($obat->id, [
                            'qty' => $obatData['qty'],
                            'harga_satuan' => $obatData['harga_satuan'],
                            'subtotal' => $subtotal,
                        ]);
                        $obat->decrement('stok', $obatData['qty']); // Kurangi stok
                        $totalBiayaObat += $subtotal;
                    }
                }
            }

            $consultation->total_biaya = $consultation->biaya_konsultasi + $totalBiayaTindakan + $totalBiayaObat;
            $consultation->save();

            DB::commit();
            return redirect()->route('consultations.index')->with('success', 'Konsultasi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat konsultasi: ' . $e->getMessage());
        }
    }

    public function show(Consultation $consultation)
    {
        $consultation->load('pelanggan', 'obats', 'medicalActions');
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $pelanggans = Pelanggan::all();
        $medicalActions = MedicalAction::all();
        $obats = Obat::all();
        $consultation->load('obats', 'medicalActions');
        return view('consultations.edit', compact('consultation', 'pelanggans', 'medicalActions', 'obats'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $request->validate([
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'doctor_name' => 'required|string|max:255',
            'tanggal_konsultasi' => 'required|date',
            'biaya_konsultasi' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'medical_actions.*.id' => 'nullable|exists:medical_actions,id',
            'medical_actions.*.biaya_override' => 'nullable|numeric|min:0',
            'obats.*.id' => 'nullable|exists:obats,id',
            'obats.*.qty' => 'nullable|integer|min:1',
            'obats.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Kembalikan stok obat yang sebelumnya terkait
            foreach ($consultation->obats as $obat) {
                $obat->increment('stok', $obat->pivot->qty);
            }

            $consultation->update([
                'pelanggan_id' => $request->pelanggan_id,
                'doctor_name' => $request->doctor_name,
                'tanggal_konsultasi' => $request->tanggal_konsultasi,
                'biaya_konsultasi' => $request->biaya_konsultasi,
                'catatan' => $request->catatan,
            ]);

            // Sinkronisasi tindakan medis
            $syncMedicalActions = [];
            $totalBiayaTindakan = 0;
            if ($request->has('medical_actions')) {
                foreach ($request->medical_actions as $actionData) {
                    if (isset($actionData['id'])) {
                        $medicalAction = MedicalAction::find($actionData['id']);
                        $biaya = $actionData['biaya_override'] ?? $medicalAction->biaya_dasar;
                        $syncMedicalActions[$medicalAction->id] = ['biaya_tindakan_override' => $biaya];
                        $totalBiayaTindakan += $biaya;
                    }
                }
            }
            $consultation->medicalActions()->sync($syncMedicalActions);

            // Sinkronisasi obat
            $syncObats = [];
            $totalBiayaObat = 0;
            if ($request->has('obats')) {
                foreach ($request->obats as $obatData) {
                    if (isset($obatData['id']) && isset($obatData['qty']) && isset($obatData['harga_satuan'])) {
                        $obat = Obat::find($obatData['id']);
                        if ($obat->stok < $obatData['qty']) {
                            DB::rollBack();
                            return back()->withInput()->with('error', 'Stok obat ' . $obat->nama . ' tidak mencukupi.');
                        }
                        $subtotal = $obatData['qty'] * $obatData['harga_satuan'];
                        $syncObats[$obat->id] = [
                            'qty' => $obatData['qty'],
                            'harga_satuan' => $obatData['harga_satuan'],
                            'subtotal' => $subtotal,
                        ];
                        $obat->decrement('stok', $obatData['qty']); // Kurangi stok baru
                        $totalBiayaObat += $subtotal;
                    }
                }
            }
            $consultation->obats()->sync($syncObats);

            $consultation->total_biaya = $consultation->biaya_konsultasi + $totalBiayaTindakan + $totalBiayaObat;
            $consultation->save();

            DB::commit();
            return redirect()->route('consultations.index')->with('success', 'Konsultasi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui konsultasi: ' . $e->getMessage());
        }
    }

    public function destroy(Consultation $consultation)
    {
        DB::beginTransaction();
        try {
            // Kembalikan stok obat yang terkait sebelum menghapus
            foreach ($consultation->obats as $obat) {
                $obat->increment('stok', $obat->pivot->qty);
            }
            $consultation->delete();
            DB::commit();
            return redirect()->route('consultations.index')->with('success', 'Konsultasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus konsultasi: ' . $e->getMessage());
        }
    }

    public function printReceipt(Consultation $consultation)
    {
        $consultation->load('pelanggan', 'obats', 'medicalActions');
        $pdf = PDF::loadView('consultations.receipt', compact('consultation'));
        return $pdf->download('struk_konsultasi_' . $consultation->id . '.pdf');
    }
}