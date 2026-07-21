<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('teacher.leave.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('teacher.leave.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:izin,sakit',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ];

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $leaveRequest = LeaveRequest::create($data);

        NotificationHelper::send(
            auth()->user(),
            'info',
            'Pengajuan ' . ucfirst($validated['type']) . ' Berhasil Dikirim',
            'Pengajuan ' . $validated['type'] . ' Anda dari tanggal ' . Carbon::parse($validated['start_date'])->format('d M Y') . ' s/d ' . Carbon::parse($validated['end_date'])->format('d M Y') . ' telah dikirim dan menunggu persetujuan admin.',
            route('teacher.leave.show', $leaveRequest),
            'file-text',
            'bg-blue-100 text-blue-600'
        );

        // Kirim notifikasi ke semua admin/operator
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationHelper::send(
                $admin,
                'warning',
                'Pengajuan ' . ucfirst($validated['type']) . ' Baru',
                auth()->user()->name . ' mengajukan ' . $validated['type'] . ' dari tanggal ' . Carbon::parse($validated['start_date'])->format('d M Y') . ' s/d ' . Carbon::parse($validated['end_date'])->format('d M Y') . '. Silakan tinjau pengajuan ini.',
                route('leaves.show', $leaveRequest),
                'file-text',
                'bg-yellow-100 text-yellow-600'
            );
        }

        return redirect()->route('teacher.leave')
            ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        // Pastikan hanya pemilik yang bisa lihat
        if (!$leaveRequest || (int) $leaveRequest->user_id !== (int) auth()->id()) {
            return redirect()->route('teacher.leave')
                ->with('error', 'Anda tidak memiliki akses ke detail pengajuan ini.');
        }

        return view('teacher.leave.show', compact('leaveRequest'));
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        if ((int) $leaveRequest->user_id !== (int) auth()->id()) {
            return redirect()->route('teacher.leave')
                ->with('error', 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
        }

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang bisa dibatalkan.');
        }

        // Delete attachment if exists
        if ($leaveRequest->attachment) {
            Storage::disk('public')->delete($leaveRequest->attachment);
        }

        $leaveRequest->delete();

        return redirect()->route('teacher.leave')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }
}
