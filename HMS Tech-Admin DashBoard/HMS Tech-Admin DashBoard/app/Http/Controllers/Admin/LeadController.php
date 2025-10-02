<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Models\BusinessDeveloper;
use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadFacebookDetail;
use App\Models\LeadFiverrDetail;
use App\Models\LeadLinkedinDetail;
use App\Models\LeadOtherDetail;
use App\Models\LeadUpworkDetail;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function index()
    {
        // Fetch all leads with latest first
        $leads = Lead::with(['project', 'client', 'businessDeveloper','facebook','linkedin','fiverr','upwork','other']) ->orderBy('created_at', 'desc')
    ->get();
        $projects = Project::all();
        $clients = Client::with('user')->get();
        $businessDevelopers = BusinessDeveloper::with('addUser')->get(); // 👈 get all BDs
        // dd($leads);
        return view('admin.pages.lead', compact('leads', 'projects', 'clients', 'businessDevelopers'));
    }
    /**
     * Store a newly created lead in storage.
     */

public function show(Lead $lead)
{
    // Load platform details
    $lead->load(['businessDeveloper', 'upwork', 'linkedin', 'facebook', 'fiverr', 'other']);

    return response()->json($lead);
}


   public function store(Request $request)
{
    $rules = [
        'lead_title'            => 'required|string|max:255',
        'status'                => 'required|in:pending,in_progress,completed,cancelled',
        'lead_get_by'           => 'required|in:facebook,linkedin,upwork,fiverr,other',
        'business_developer_id' => 'required|exists:business_developers,id',
        'project_id'            => 'nullable|exists:projects,id',
        'client_id'             => 'nullable|exists:clients,id',
        'lead_description'      => 'nullable|string',
        'expected_budget'       => 'nullable|numeric|min:0',
        'expected_start_date'   => 'nullable|date',
        'contact_person'        => 'nullable|string|max:255',
        'contact_email'         => 'nullable|email',
        'contact_phone'         => 'nullable|string|max:20',
        'next_follow_up'        => 'nullable|date',
        'notes'                 => 'nullable|string',
        // Nested platform fields
        'upwork'   => 'array',
        'linkedin' => 'array',
        'facebook' => 'array',
        'fiverr'   => 'array',
        'other'    => 'array',
    ];

    $validated = $request->validate($rules);

    // Use DB::transaction() helper for simplicity
    DB::transaction(function () use ($validated, $request) {
        // Create lead
        $lead = Lead::create($validated);

        // Save platform details
        $this->savePlatformDetails($request, $lead->id);
    });

    return redirect()->route('leads.index')->with('success', 'Lead created successfully!');
}

    /**
     * Update the specified lead in storage.
     */
   public function update(Request $request, Lead $lead)
{
    $rules = [
        'lead_title'            => 'required|string|max:255',
        'status'                => 'required|in:pending,in_progress,completed,cancelled',
        'lead_get_by'           => 'required|in:facebook,linkedin,upwork,fiverr,other',
        'business_developer_id' => 'required|exists:business_developers,id',
        'project_id'            => 'nullable|exists:projects,id',
        'client_id'             => 'nullable|exists:clients,id',
        'lead_description'      => 'nullable|string',
        'expected_budget'       => 'nullable|numeric|min:0',
        'expected_start_date'   => 'nullable|date',
        'contact_person'        => 'nullable|string|max:255',
        'contact_email'         => 'nullable|email',
        'contact_phone'         => 'nullable|string|max:20',
        'next_follow_up'        => 'nullable|date',
        'notes'                 => 'nullable|string',
        // Nested platform fields
        'upwork'   => 'array',
        'linkedin' => 'array',
        'facebook' => 'array',
        'fiverr'   => 'array',
        'other'    => 'array',
    ];

    $validated = $request->validate($rules);

    DB::transaction(function () use ($validated, $request, $lead) {
         if ($lead->lead_get_by !== $request->lead_get_by) {
            $lead->upwork()->delete();
            $lead->linkedin()->delete();
            $lead->facebook()->delete();
            $lead->fiverr()->delete();
            $lead->other()->delete();
         }
        // Update lead
        $lead->update($validated);

        // Save/update platform details
        $this->savePlatformDetails($request, $lead->id);
    });

    return redirect()->back()->with('success', 'Lead updated successfully!');
}


    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead)
    {
        try {
            $lead->delete(); // ⚠️ Will also delete platform details if you set cascadeOnDelete() in migrations
            return redirect()->back()->with('success', 'Lead deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Save or update platform details based on the platform type.
     */
    // protected function savePlatformDetails($request, $leadId)
    // {
    //     switch ($request->lead_get_by) {
    //         case 'linkedin':
    //             LeadLinkedinDetail::updateOrCreate(
    //                 ['lead_id' => $leadId],
    //                 $request->only(['company_name', 'profile_link', 'job_post_url', 'message_sent'])
    //             );
    //             break;

    //         case 'upwork':
    //             LeadUpworkDetail::updateOrCreate(
    //                 ['lead_id' => $leadId],
    //                 $request->only(['project_title', 'proposal_cover_letter', 'connect_bids', 'job_url'])
    //             );
    //             break;

    //         case 'facebook':
    //             LeadFacebookDetail::updateOrCreate(
    //                 ['lead_id' => $leadId],
    //                 $request->only(['page_name', 'ad_campaign_name', 'post_url', 'inquiry_message'])
    //             );
    //             break;

    //         case 'fiverr':
    //             LeadFiverrDetail::updateOrCreate(
    //                 ['lead_id' => $leadId],
    //                 $request->only(['gig_title', 'buyer_request_message', 'offer_amount', 'buyer_username'])
    //             );
    //             break;

    //         case 'other':
    //             LeadOtherDetail::updateOrCreate(
    //                 ['lead_id' => $leadId],
    //                 $request->only(['platform_name', 'platform_url', 'campaign_name', 'inquiry_message', 'estimated_budget', 'contact_method'])
    //             );
    //             break;
    //     }
    // }
    protected function savePlatformDetails($request, $leadId)
{
    switch ($request->lead_get_by) {
        case 'linkedin':
            LeadLinkedinDetail::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'company_name' => $request->input('linkedin.company_name'),
                    'profile_link' => $request->input('linkedin.profile_link'),
                    'job_post_url' => $request->input('linkedin.job_post_url'),
                    'message_sent' => $request->input('linkedin.message_sent'),
                ]
            );
            break;

        case 'upwork':
            LeadUpworkDetail::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'project_title' => $request->input('upwork.project_title'),
                    'proposal_cover_letter' => $request->input('upwork.proposal_cover_letter'),
                    'connect_bids' => $request->input('upwork.connect_bids'),
                    'job_url' => $request->input('upwork.job_url'),
                ]
            );
            break;

        case 'facebook':
            LeadFacebookDetail::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'page_name' => $request->input('facebook.page_name'),
                    'ad_campaign_name' => $request->input('facebook.ad_campaign_name'),
                    'post_url' => $request->input('facebook.post_url'),
                    'inquiry_message' => $request->input('facebook.inquiry_message'),
                ]
            );
            break;

        case 'fiverr':
            LeadFiverrDetail::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'gig_title' => $request->input('fiverr.gig_title'),
                    'buyer_request_message' => $request->input('fiverr.buyer_request_message'),
                    'offer_amount' => $request->input('fiverr.offer_amount'),
                    'buyer_username' => $request->input('fiverr.buyer_username'),
                ]
            );
            break;

        case 'other':
            LeadOtherDetail::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'platform_name' => $request->input('other.platform_name'),
                    'platform_url' => $request->input('other.platform_url'),
                    'campaign_name' => $request->input('other.campaign_name'),
                    'inquiry_message' => $request->input('other.inquiry_message'),
                    'estimated_budget' => $request->input('other.estimated_budget'),
                    'contact_method' => $request->input('other.contact_method'),
                ]
            );
            break;
    }
}

}
