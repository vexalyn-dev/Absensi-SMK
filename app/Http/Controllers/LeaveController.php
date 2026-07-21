<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total'    => $leaveRequests->count(),
            'pending'  => $leaveRequests->where('status', 'pending')->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
        ];

        return view('leaves.index', compact('leaveRequests', 'stats'));
    }

    public function latest()
    {
        $leaveRequests = LeaveRequest::with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'total'    => $leaveRequests->count(),
                'pending'  => $leaveRequests->where('status', 'pending')->count(),
                'approved' => $leaveRequests->where('status', 'approved')->count(),
                'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            ],
            'leaves' => $leaveRequests->map(fn (LeaveRequest $leave) => [
                'id' => $leave->id,
                'teacher_name' => $leave->user?->name ?? 'Guru',
                'teacher_photo_url' => $leave->user?->photo_url,
                'type' => $leave->type,
                'type_text' => ucfirst($leave->type),
                'status' => $leave->status,
                'status_text' => ucfirst($leave->status),
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'duration' => $leave->duration,
                'reason' => $leave->reason,
                'admin_notes' => $leave->admin_notes,
                'show_url' => route('leaves.show', $leave),
                'approve_url' => route('leaves.approve', $leave),
                'reject_url' => route('leaves.reject', $leave),
            ]),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->to(route('teacher.dashboard') . '#izin');
        }

        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'       => 'required|in:izin,sakit',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'user_id'    => auth()->id(),
            'type'       => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'reason'     => $validated['reason'],
            'status'     => 'pending',
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $leaveRequest = LeaveRequest::create($data);

        if (auth()->user()->isAdmin()) {
            return redirect()->route('leaves.index')->with('success', 'Pengajuan izin berhasil dikirim!');
        }

        return redirect()->route('teacher.leave')->with('success', 'Pengajuan izin berhasil dikirim!');
    }

    public function show(LeaveRequest $leave)
    {
        if (!auth()->user()->isAdmin() && (int) $leave->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('teacher.leave.show', $leave);
        }

        $leave->load(['user', 'approvedBy']);

        return view('leaves.show', compact('leave'));
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Kirim notifikasi ke guru
        \App\Helpers\NotificationHelper::send(
            $leave->user,
            'success',
            'Pengajuan ' . ucfirst($leave->type) . ' Disetujui',
            'Pengajuan ' . $leave->type . ' Anda dari tanggal ' . $leave->start_date->format('d M Y') . ' s/d ' . $leave->end_date->format('d M Y') . ' telah disetujui oleh admin.',
            route('teacher.leave.show', $leave),
            'check-circle',
            'bg-green-100 text-green-600'
        );

        return back()->with('success', 'Pengajuan berhasil disetujui');
    }

    public function reject(LeaveRequest $leave)
    {
        $leave->update([
            'status'      => 'rejected',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Kirim notifikasi ke guru
        \App\Helpers\NotificationHelper::send(
            $leave->user,
            'error',
            'Pengajuan ' . ucfirst($leave->type) . ' Ditolak',
            'Pengajuan ' . $leave->type . ' Anda dari tanggal ' . $leave->start_date->format('d M Y') . ' s/d ' . $leave->end_date->format('d M Y') . ' ditolak. Alasan: ' . (request('admin_notes') ?? '-'),
            route('teacher.leave.show', $leave),
            'x-circle',
            'bg-red-100 text-red-600'
        );

        return back()->with('success', 'Pengajuan ditolak');
    }

    public function myLeaves()
    {
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('leaves.my-leaves', compact('leaveRequests'));
    }
}
