<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\DoctorLeave;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveController extends Controller
{

    public function index(Request $request)
    {
        $query = DoctorLeave::with('doctor');

        // Doctor filter
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Leave type filter ✅ (MISSING earlier)
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Date range filter ✅ (correct logic)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            });
        } elseif ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        $doctors = User::where('role', 'doctor')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $leaves->items(),
                'pagination' => [
                    'current_page' => $leaves->currentPage(),
                    'last_page' => $leaves->lastPage(),
                    'per_page' => $leaves->perPage(),
                    'total' => $leaves->total(),
                    'from' => $leaves->firstItem(),
                    'to' => $leaves->lastItem(),
                ],
            ]);
        }

        return view('admin.leave.index', compact('leaves', 'doctors'));
    }




    // public function index(Request $request)
    // {
    //     $query = DoctorLeave::query();

    //     // Filters
    //     if ($request->filled('doctor_id')) {
    //         $query->where('doctor_id', $request->doctor_id);
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('start_date')) {
    //         $query->whereDate('start_date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('end_date', '<=', $request->end_date);
    //     }

    //     $leaves = $query->with('doctor')->orderBy('created_at', 'desc')->paginate(10);
    //     $doctors = User::where('role', 'doctor')->get();

    //     // Use wantsJson() instead of ajax()
    //     if ($request->wantsJson()) {
    //         return response()->json([
    //             'data' => $leaves->items(),
    //             'pagination' => [
    //                 'current_page' => $leaves->currentPage(),
    //                 'last_page' => $leaves->lastPage(),
    //                 'per_page' => $leaves->perPage(),
    //                 'total' => $leaves->total(),
    //                 'from' => $leaves->firstItem(),
    //                 'to' => $leaves->lastItem(),
    //             ],
    //         ]);
    //     }

    //     return view('admin.leave.index', compact('leaves', 'doctors'));
    // }
}
