<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authorized users
    }

    public function rules(): array
    {
        // Base rules (common fields)
        $rules = [
            'lead_title'            => 'required|string|max:255',
            'status'                => 'required|in:pending,in_progress,completed,cancelled',
            'lead_get_by'           => 'required|in:facebook,linkedin,upwork,fiverr,other',
            'business_developer_id' => 'required|exists:users,id',
            // Nullable fields
            'project_id'        => 'nullable|exists:projects,id',
            'client_id'         => 'nullable|exists:clients,id',
            'lead_description'  => 'nullable|string',
            'expected_budget'   => 'nullable|numeric|min:0',
            'expected_start_date' => 'nullable|date',
            'contact_person'    => 'nullable|string|max:255',
            'contact_email'     => 'nullable|email',
            'contact_phone'     => 'nullable|string|max:20',
            'next_follow_up'    => 'nullable|date',
            'notes'             => 'nullable|string',
        ];

        // Platform-specific rules
        switch (request()->input('lead_get_by')) {
            case 'upwork':
                $rules['upwork.project_title'] = 'required|string|max:255';
                $rules['upwork.proposal_cover_letter'] = 'required|string';
                $rules['upwork.connect_bids'] = 'required|integer|min:1';

                break;

            case 'linkedin':
                $rules['linkedin.company_name'] = 'required|string|max:255';
                $rules['linkedin.profile_link'] = 'required|url';
                $rules['linkedin.job_post_url'] = 'nullable|url';
                break;

            case 'facebook':
                $rules['facebook.page_name'] = 'required|string|max:255';
                $rules['facebook.inquiry_message'] = 'required|string';
                break;

            case 'fiverr':
                $rules['fiverr.gig_title'] = 'required|string|max:255';
                $rules['fiverr.buyer_request_message'] = 'required|string';
                break;

            case 'other':
                $rules['other.platform_name'] = 'required|string|max:255';
                $rules['other.inquiry_message'] = 'required|string';
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'upwork.project_title.required' => 'Project title is required for Upwork leads.',
            'linkedin.profile_link.required' => 'Profile link is required for LinkedIn leads.',
            'facebook.page_name.required' => 'Page name is required for Facebook leads.',
            'fiverr.gig_title.required' => 'Gig title is required for Fiverr leads.',
            'other.platform_name.required' => 'Platform name is required for other leads.',
        ];
    }
}
