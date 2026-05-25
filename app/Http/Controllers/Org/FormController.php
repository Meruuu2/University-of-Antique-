<?php

namespace App\Http\Controllers\Org;

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

        // Org can request any active facility (including college-owned AVRs).
        $coreFacilities = Facility::where('is_active', true)
            ->orderBy('name')
            ->get();




        return view('org.requests.facilities_create', compact('coreFacilities', 'user'));

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

        $response = $this->handleStoreFacilities($request);

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
        return 'org';
    }

    protected function requesterUnit(): string
    {
        return Auth::user()->organization_name;
    }

    protected function submittedRedirectRoute(): string
    {
        return 'org.dashboard';
    }

    /**
     * List the current org staff member's requests.
     */
    public function indexFacilities()
    {
        $submissions = $this->handleIndexFacilities();

        return view('org.requests.facilities_index', compact('submissions'));
    }
}

