<?php

namespace App\Services\Admin;

use App\Models\Specialty;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class specialitiesServices
{
    public function getList($request)
    {
        $query = Specialty::query();

        // Search filter
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Paginate results
        $data = $query->orderBy('id', 'DESC')->paginate(10);

        return [
            'data' => $data,
        ];
    }

    public function store(Request $request)
    {
        $id = $request->id ?? null;

        $nameRule = 'required|string|max:50|regex:/^[a-zA-Z\s]+$/|unique:specialties,name';

        if ($id) {
            $nameRule = 'required|string|max:50|regex:/^[a-zA-Z\s]+$/|unique:specialties,name,'.$id;
        }

        $rules = [
            'name' => $nameRule,
            'description' => 'required|string|max:50|regex:/^[a-zA-Z\s.,]+$/',
            'status' => 'required|in:active,inactive',
        ];

        $messages = [
            'name.unique' => 'This name is already taken',
            'name.required' => 'Please enter name',
            'name.regex' => 'Name can only contain letters and spaces',
            'description.required' => 'Please enter description',
            'description.regex' => 'Description can only contain letters, spaces, commas and periods',
            'status.required' => 'Please select status',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'status' => 400,
                'errors' => $validator->errors(),
            ];
        }

        try {
            if ($id) {
                $specialty = Specialty::find($id);
                if (! $specialty) {
                    return ['status' => 404, 'msg' => 'Specialty not found'];
                }
                $msg = 'Specialty has been updated successfully.';
            } else {
                $specialty = new Specialty;
                $msg = 'Specialty has been added successfully.';
            }

            $specialty->name = $request->name;
            $specialty->description = $request->description;
            $specialty->status = $request->status;
            $specialty->save();

            return ['status' => 200, 'msg' => $msg];

        } catch (Exception $ex) {
            return ['status' => 400, 'msg' => $ex->getMessage()];
        }
    }

    public function toggleStatus(Request $request)
    {
        $specialty = Specialty::find($request->id);

        if (! $specialty) {
            return [
                'status' => 404,
                'msg' => 'Not Found',
            ];
        }

        // Toggle status
        $specialty->status = ($specialty->status == 'active') ? 'inactive' : 'active';
        $specialty->save();

        return [
            'status' => 200,
            'msg' => 'Status updated successfully',
            'new_status' => $specialty->status,
            'specialty' => $specialty,
        ];
    }

    public function destroy($id)
    {
        try {
            $specialty = Specialty::find($id);

            if (! $specialty) {
                return response()->json([
                    'status' => 404,
                    'msg' => 'Specialty not found.',
                ], 404);
            }
            $specialty->delete();

            return response()->json([
                'status' => 200,
                'msg' => 'Specialty deleted successfully!',
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'status' => 400,
                'msg' => 'Failed to delete specialty: '.$ex->getMessage(),
            ], 400);
        }
    }
}
