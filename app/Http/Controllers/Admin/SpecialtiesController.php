<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use App\Services\Admin\SpecialitiesServices;
use Illuminate\Http\Request;

class SpecialtiesController extends Controller
{
    protected $specialitiesServices;

    public function __construct(SpecialitiesServices $specialities)
    {
        $this->specialitiesServices = $specialities;
    }

    public function index()
    {
        return view('admin.specialities.index');
    }

    public function getModel(Request $request)
    {
        $data = [];

        if (isset($request->id) && $request->id != '') {
            $data = Specialty::where('id', $request->id)->first();
        }

        return view('admin.specialities.getmodel', compact('data'));
    }

    public function getList(Request $request)
    {
        return response()->json(
            $this->specialitiesServices->getList($request)
        );
    }

    public function store(Request $request)
    {
        $result = $this->specialitiesServices->store($request);

        return response()->json($result);
    }

    public function toggleStatus(Request $request)
    {
        $result = $this->specialitiesServices->toggleStatus($request);

        return response()->json($result);
    }

    public function destroy($id)
    {
        return $this->specialitiesServices->destroy($id);
    }

    public function view($id)
    {
        $specialty = Specialty::findOrFail($id);
        $doctors = \App\Models\User::where('role', 'doctor')
            ->whereHas('doctorProfile', function ($query) use ($id) {
                $query->where('specialty_id', $id);
            })
            ->get();

        // Check if it's an AJAX request (for modal)
        if (request()->ajax()) {
            return view('admin.specialities.view-modal', compact('specialty', 'doctors'));
        }

        // Return full page view for direct access
        return view('admin.specialities.view', compact('specialty', 'doctors'));
    }
}
