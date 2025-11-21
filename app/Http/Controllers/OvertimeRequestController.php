<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OvertimeRequestController extends Controller
{
    // Show OT request form
    public function create()
    {
        $branches = Branch::all();
        $departments = Department::all();

        return view('overtime.form', compact('branches', 'departments'));
    }

    // Store OT request + generate QR
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        $ot = OvertimeRequest::create($request->all());

        // Generate QR URL
        $qrUrl = route('overtime.clockin', $ot->id);

        // Generate QR PNG
        $qrImage = QrCode::format('png')->size(300)->generate($qrUrl);

        $fileName = 'qr_' . $ot->id . '.png';
        Storage::disk('public')->put('qr/' . $fileName, $qrImage);

        $ot->update(['qr_code' => 'qr/' . $fileName]);

        return view('overtime.success', compact('ot'));
    }

    // Clock-in via QR
    public function clockin($id)
    {
        $ot = OvertimeRequest::findOrFail($id);

        if ($ot->clocked_in_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Already clocked in at ' . $ot->clocked_in_at,
            ]);
        }

        $ot->update(['clocked_in_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Clock-in successful at ' . now(),
        ]);
    }
}
