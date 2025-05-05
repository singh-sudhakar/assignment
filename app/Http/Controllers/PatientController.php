<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // You can adjust the number of items per page as needed
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');

        $query = Patient::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Paginate patients
        $patients =  $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($patients);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'group' => 'required|in:A,B,C,D',
            'wait_time' => 'required|integer',
            'consultation_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $validator->validated();
        $data['patient_id'] = Str::uuid()->toString();
        $data['week'] = Carbon::parse($data['consultation_date'])->weekOfYear;
        $data['month'] = Carbon::parse($data['consultation_date'])->format('F');

        $patient = Patient::create($data);

        return response()->json($patient, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

   /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'group' => 'required|in:A,B,C,D',
            'wait_time' => 'required|integer',
            'consultation_date' => 'required|date',
        ]);

        $data['week'] = \Carbon\Carbon::parse($data['consultation_date'])->weekOfYear;
        $data['month'] = \Carbon\Carbon::parse($data['consultation_date'])->format('F');

        $patient->update($data);

        return response()->json([
            'message' => 'Patient updated successfully',
            'patient' => $patient,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->json(['message' => 'Patient deleted']);
    }

    public function groupStats()
    {
        $data = Patient::select('group', DB::raw('count(*) as total'))
            ->groupBy('group')
            ->get();

        return response()->json($data);
    }

    public function waitStats()
    {
        $weekly = Patient::select(
            DB::raw('WEEK(consultation_date) as week'),
            'group',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('WEEK(consultation_date)'), 'group') // Ensure WEEK(consultation_date) is in GROUP BY
        ->get();

        $monthly = Patient::select(
            DB::raw('MONTH(consultation_date) as month'),
            'group',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('MONTH(consultation_date)'), 'group') // Ensure MONTH(consultation_date) is in GROUP BY
        ->get();

        return response()->json([
            'weekly' => $weekly,
            'monthly' => $monthly,
        ]);
    }
}
