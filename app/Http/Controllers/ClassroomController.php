<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('teachingSchedules')
            ->orderByRaw("FIELD(type, 'regular', 'shared')")
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->groupBy(function($classroom) {
                return $classroom->level ?? $classroom->class_level ?? 'Ruangan Bersama';
            });

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|in:regular,shared',
            'name'          => 'required|string|max:255',
            'major'         => 'required_if:type,regular|nullable|string|max:50',
            'level'         => 'required|in:X,XI,XII',
            'location_type' => 'required_if:type,shared|nullable|string|max:50',
            'code'          => 'required|string|max:50|unique:classrooms,code',

            'description'   => 'nullable|string|max:500',
            'is_active'     => 'nullable|boolean',
        ]);

        $classroom = Classroom::create([
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'major'         => $validated['major'] ?? null,
            'level'         => $validated['level'] ?? null,
            'location_type' => $validated['location_type'] ?? null,
            'is_shared'     => $validated['type'] === 'shared',
            'code'          => strtoupper($validated['code']),

            'description'   => $validated['description'] ?? null,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ]);

        // Generate QR Code image
        $qrData = "{$classroom->id}|{$classroom->code}";
        $qrPath = $this->generateQRCode($qrData, $classroom->code);
        $classroom->update(['qr_code' => $qrPath]);

        return redirect()->route('classrooms.index')
            ->with('success', "Kelas '{$classroom->name}' berhasil ditambahkan dengan kode {$classroom->code}");
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'type'          => 'required|in:regular,shared',
            'name'          => 'required|string|max:255',
            'major'         => 'required_if:type,regular|nullable|string|max:50',
            'level'         => 'required|in:X,XI,XII',
            'location_type' => 'required_if:type,shared|nullable|string|max:50',
            'code'          => 'required|string|max:50|unique:classrooms,code,' . $classroom->id,

            'description'   => 'nullable|string|max:500',
            'is_active'     => 'nullable|boolean',
        ]);

        $classroom->update([
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'major'         => $validated['major'] ?? null,
            'level'         => $validated['level'] ?? null,
            'location_type' => $validated['location_type'] ?? null,
            'is_shared'     => $validated['type'] === 'shared',
            'code'          => strtoupper($validated['code']),

            'description'   => $validated['description'] ?? null,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ]);

        // Regenerate QR Code jika kode berubah
        $qrData = "{$classroom->id}|{$classroom->code}";
        $qrPath = $this->generateQRCode($qrData, $classroom->code);
        $classroom->update(['qr_code' => $qrPath]);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Classroom $classroom)
    {
        // Hapus file QR Code jika ada
        if ($classroom->qr_code && Storage::disk('public')->exists($classroom->qr_code)) {
            Storage::disk('public')->delete($classroom->qr_code);
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    public function qrCode(Classroom $classroom)
    {
        return view('classrooms.qr', compact('classroom'));
    }

    /**
     * Generate QR Code image dan simpan ke storage.
     */
    private function generateQRCode(string $data, string $filename): string
    {
        $directory = 'qrcodes/classrooms';

        // Pastikan direktori ada
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $path = "{$directory}/{$filename}.svg";

        try {
            $qrImage = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($data);

            Storage::disk('public')->put($path, $qrImage);
        } catch (\Exception $e) {
            // Jika package QR Code tidak tersedia, simpan path saja
            // QR data masih bisa diakses via qr_token di model
            $path = null;
        }

        return $path;
    }
}