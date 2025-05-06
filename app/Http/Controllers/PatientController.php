<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');

        $query = Patient::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $patients = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($patients);
    }

    /**
     * Store a newly created patient.
     *
     * @param StorePatientRequest $request
     * @return JsonResponse
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['patient_id'] = Str::uuid()->toString();
        $data['week'] = Carbon::parse($data['consultation_date'])->weekOfYear;
        $data['month'] = Carbon::parse($data['consultation_date'])->format('F');

        $patient = Patient::create($data);

        return response()->json($patient, 201);
    }

    /**
     * Display the specified patient.
     *
     * @param Patient $patient
     * @return JsonResponse
     */
    public function show(Patient $patient): JsonResponse
    {
        return response()->json($patient);
    }

    /**
     * Update the specified patient.
     *
     * @param UpdatePatientRequest $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $data = $request->validated();
        $data['week'] = Carbon::parse($data['consultation_date'])->weekOfYear;
        $data['month'] = Carbon::parse($data['consultation_date'])->format('F');

        $patient->update($data);

        return response()->json([
            'message' => 'Patient updated successfully',
            'patient' => $patient,
        ]);
    }

    /**
     * Remove the specified patient.
     *
     * @param Patient $patient
     * @return JsonResponse
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();

        return response()->json(['message' => 'Patient deleted']);
    }

    /**
     * Get group-based statistics.
     *
     * @return JsonResponse
     */
    public function groupStats(): JsonResponse
    {
        $data = Patient::select('group', DB::raw('count(*) as total'))
            ->groupBy('group')
            ->get();

        return response()->json($data);
    }

    /**
     * Get wait time statistics by week and month.
     *
     * @return JsonResponse
     */
    public function waitStats(): JsonResponse
    {
        $weekly = Patient::select(
            DB::raw('WEEK(consultation_date) as week'),
            'group',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('WEEK(consultation_date)'), 'group')
        ->get();

        $monthly = Patient::select(
            DB::raw('MONTH(consultation_date) as month'),
            'group',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('MONTH(consultation_date)'), 'group')
        ->get();

        return response()->json([
            'weekly' => $weekly,
            'monthly' => $monthly,
        ]);
    }
}
