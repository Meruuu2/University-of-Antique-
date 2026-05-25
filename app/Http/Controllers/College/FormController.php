<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SubmitsFacilitiesForm;
use App\Http\Requests\StoreFacilitiesUtilizationRequest;
use App\Models\Facility;
use App\Models\FormSubmission;
use App\Models\Signatory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    use SubmitsFacilitiesForm;

    public function __construct(protected NotificationService $notifications)
    {
    }

    /**
     * Show the Facilities Utilization online request form.
     */
    public function createFacilities()
    { 
        $user = Auth::user();

        // Facility request dropdown:
        // - allow all GSU-owned and College-owned facilities
        $coreFacilities = Facility::where('is_active', true)
            ->where(function ($q) {
                $q->where('owner_type', 'gsu')
                  ->orWhere('owner_type', 'college');
            })
            ->orderBy('name')
            ->get();

        // Also get a list of facilities specific to the user's college to display as a hint
        $collegeFacilities = Facility::where('is_active', true)
            ->where('owner_type', 'college')
            ->where('owner_college', $user->college_name)
            ->orderBy('name')
            ->get();

        // Signatories (dean + program heads) for this college
        $deans = Signatory::where('type', 'dean')
            ->forCollege($user->college_id, $user->college_name)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $programHeads = Signatory::where('type', 'program_head')
            ->forCollege($user->college_id, $user->college_name)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('college.requests.facilities_create', compact(
            'coreFacilities',
            'collegeFacilities',
            'user',
            'deans',
            'programHeads'
        ));

    }

    /**
     * Store Facilities Utilization request as a JSON form submission.
     *
     * Delegates core creation/notifications to SubmitsFacilitiesForm, then enriches the submission
     * payload with extra "Noted by" fields so we don't lose the signatory feature.
     */
    public function storeFacilities(StoreFacilitiesUtilizationRequest $request)
    {
        $user = Auth::user();

        $facilityId = $request->input('facility_id');

        $notedName = null;
        if ($request->filled('noted_signatory_id') && $request->input('noted_signatory_id') !== 'custom') {
            [$type, $id] = explode(':', $request->input('noted_signatory_id')) + [null, null];
            if ($id) {
                $signatory = Signatory::find($id);
                if ($signatory) {
                    $notedName = $signatory->name;
                }
            }
        } elseif ($request->filled('noted_signatory_custom')) {
            $notedName = $request->input('noted_signatory_custom');
        }

        $notedDatetime = $notedName ? now()->format('Y-m-d H:i:s') : null;

        $response = $this->handleStoreFacilities($request, $facilityId);

        $submission = FormSubmission::where('requester_id', $user->id)
            ->where('type', 'facilities_utilization')
            ->latest('id')
            ->first();

        if ($submission) {
            $payload = $submission->payload ?? [];
            $payload['noted_signatory_name'] = $notedName;
            $payload['noted_datetime']       = $notedDatetime;
            $submission->payload = $payload;
            $submission->save();
        }

        return $response;
    }

    protected function requesterType(): string
    {
        return 'college';
    }

    protected function requesterUnit(): string
    {
        return Auth::user()->college_name;
    }

    protected function submittedRedirectRoute(): string
    {
        return 'college.dashboard';
    }

    /**
     * List the current college staff member's requests.
     */
    public function indexFacilities()
    {
        $submissions = $this->handleIndexFacilities();

        return view('college.requests.facilities_index', compact('submissions'));
    }


    /**
     * Show a single facilities utilization request for this college staff user.
     */
    public function showFacilities(FormSubmission $submission)
    {
        $user = Auth::user();

        if (
            $submission->type !== 'facilities_utilization' ||
            $submission->requester_id !== $user->id
        ) {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facilities = collect();

        if (!empty($payload['facility_ids'])) {
            $facilities = Facility::whereIn('id', $payload['facility_ids'])->get();
        }

        return view('college.requests.facilities_show', compact('submission', 'payload', 'facilities'));
    }
}