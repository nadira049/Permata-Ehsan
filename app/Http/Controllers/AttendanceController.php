<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role;
            if (!in_array($role, ['teacher', 'admin', 'parent'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $year = $request->input('year', '');
        $user = auth()->user();
        $childrenQuery = \App\Models\Child::with('class');

        if ($user->role === 'parent') {
            // Only show this parent's children
            $childrenQuery->where('user_id', $user->id);
        } else {
            // For teachers/admins, filter by year if set
            if (in_array($year, ['4', '5', '6'])) {
                $childrenQuery->whereHas('class', function($q) use ($year) {
                    $q->where('year', $year);
                });
            }
        }

        $children = $childrenQuery->get();
        $attendances = \App\Models\Attendance::where('date', $date)->get()->keyBy('child_id');
        return view('attendance.index', compact('children', 'attendances', 'date', 'year'));
    }

    public function updateStatus(Request $request, \App\Models\Child $child)
    {
        $validated = $request->validate([
            'status' => 'required|in:Attend,Absent,Late',
            'date' => 'required|date',
        ]);
        $attendance = \App\Models\Attendance::firstOrNew([
            'child_id' => $child->id,
            'date' => $validated['date'],
        ]);
        // Prevent status change if already confirmed
        if ($attendance->exists && $attendance->confirmed) {
            return response()->json(['error' => 'Status already confirmed.'], 403);
        }
        $attendance->status = $validated['status'];
        $attendance->confirmed = true;
        $attendance->time = now();
        $attendance->save();
        // Force fresh array with all fields
        return response()->json(['success' => true, 'attendance' => $attendance->fresh()->toArray()]);
    }

    public function updateComment(Request $request, \App\Models\Child $child)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);
        $attendance = \App\Models\Attendance::firstOrNew([
            'child_id' => $child->id,
            'date' => $validated['date'],
        ]);
        $attendance->comment = $validated['comment'];
        $attendance->save();
        return response()->json(['success' => true]);
    }

    public function exportPdf(Request $request)
    {
        $year = $request->input('year', 'Year 4');
        $month = $request->input('month');
        if ($month) {
            // $month format: YYYY-MM
            $start = $month . '-01';
            $end = date('Y-m-t', strtotime($start));
            $children = \App\Models\Child::where('year', $year)->get();
            $attendances = \App\Models\Attendance::whereBetween('date', [$start, $end])
                ->whereIn('child_id', $children->pluck('id'))
                ->orderBy('date')
                ->get()
                ->groupBy('date');
            $data = [
                'children' => $children,
                'attendances' => $attendances,
                'month' => $month,
                'year' => $year,
                'isMonth' => true,
            ];
            if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('attendance.pdf', $data);
                if ($request->query('download')) {
                    return $pdf->download('attendance_' . $year . '_' . $month . '.pdf');
                } else {
                    return $pdf->stream('attendance_' . $year . '_' . $month . '.pdf');
                }
            }
            return view('attendance.pdf', $data);
        }
        // Fallback: return view for debugging
        return view('attendance.pdf', $data);
    }
} 