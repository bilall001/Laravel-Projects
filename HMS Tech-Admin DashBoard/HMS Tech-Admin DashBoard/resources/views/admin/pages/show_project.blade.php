@extends('admin.layouts.main')
@section('title')
Project - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container mt-5">
   <form id="leadForm" method="POST" action="{{ route('leads.fields') }}">
                            @csrf
                            {{-- <input type="hidden" name="id" id="lead_id"> --}}

                            <div class="modal-header">
                                <h5 class="modal-title" id="leadModalLabel">Create Lead</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row">

                                    <!-- General Fields -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="business_developer_id">Business Developer</label>
                                            <select class="form-control" id="business_developer_id" name="business_developer_id"
                                                required>
                                                <option value="">-- Select Business Developer --</option>
                                                @foreach ($businessDevelopers as $bd)
                                                    <option value="{{ $bd->id }}">
                                                        {{ $bd->addUser->name ?? 'Unnamed BD' }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Lead Type</label>
                                            <select class="form-control" id="lead_type" name="lead_type">
                                                <option value="">-- Select Lead Type --</option>
                                                <option value="new">New Lead</option>
                                                <option value="existing">Pre-existing Lead</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row" id="projectClientWrapper" style="display: none;">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="project_id">Project</label>
                                                <select class="form-control" id="project_id" name="project_id">
                                                    <option value="">-- Select Project --</option>
                                                    @foreach ($projects as $project)
                                                        <option value="{{ $project->id }}"
                                                            {{ isset($lead) && $lead->project_id == $project->id ? 'selected' : '' }}>
                                                            {{ $project->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="client_id">Client</label>
                                                <select class="form-control" id="client_id" name="client_id">
                                                    <option value="">-- Select Client --</option>
                                                    @foreach ($clients as $client)
                                                        <option value="{{ $client->id }}"
                                                            {{ isset($lead) && $lead->client_id == $client->id ? 'selected' : '' }}>
                                                            {{ $client->user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lead_title">Lead Title</label>
                                            <input type="text" class="form-control" id="lead_title" name="lead_title"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lead_description">Lead Description</label>
                                            <textarea class="form-control" id="lead_description" name="lead_description" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="in_progress"
                                                    {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed"
                                                    {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled"
                                                    {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lead_get_by">Lead Source</label>
                                            <select class="form-control" id="lead_get_by" name="lead_get_by" required>
                                                <option value="">Select Source</option>
                                                <option value="upwork">Upwork</option>
                                                <option value="linkedin">LinkedIn</option>
                                                <option value="facebook">Facebook</option>
                                                <option value="fiverr">Fiverr</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expected_budget">Expected Budget</label>
                                            <input type="number" class="form-control" id="expected_budget"
                                                name="expected_budget">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expected_start_date">Expected Start Date</label>
                                            <input type="date" class="form-control" id="expected_start_date"
                                                name="expected_start_date">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contact_person">Contact Person</label>
                                            <input type="text" class="form-control" id="contact_person"
                                                name="contact_person">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contact_email">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email"
                                                name="contact_email">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contact_phone">Contact Phone</label>
                                            <input type="text" class="form-control" id="contact_phone"
                                                name="contact_phone">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expected_start_date">Next Follow up</label>
                                            <input type="date" class="form-control" id="next_follow_up"
                                                name="next_follow_up">
                                        </div>
                                    </div>
                                </div>

                                <!-- Platform Specific Fields -->
                                <div id="platformFields">
                                    <!-- Upwork -->
                                    <div class="platform-field d-none" id="upworkFields">
                                        <h5>Upwork Details</h5>
                                        <div class="form-group">
                                            <label>Project Title</label>
                                            <input type="text" name="upwork[project_title]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Proposal Cover Letter</label>
                                            <textarea name="upwork[proposal_cover_letter]" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Connect Bids</label>
                                            <input type="number" name="upwork[connect_bids]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Job URL</label>
                                            <input type="url" name="upwork[job_url]" class="form-control">
                                        </div>
                                    </div>

                                    <!-- LinkedIn -->
                                    <div class="platform-field d-none" id="linkedinFields">
                                        <h5>LinkedIn Details</h5>
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="text" name="linkedin[company_name]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Profile Link</label>
                                            <input type="url" name="linkedin[profile_link]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Job Post URL</label>
                                            <input type="url" name="linkedin[job_post_url]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Message Sent</label>
                                            <textarea name="linkedin[message_sent]" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <!-- Facebook -->
                                    <div class="platform-field d-none" id="facebookFields">
                                        <h5>Facebook Details</h5>
                                        <div class="form-group">
                                            <label>Page Name</label>
                                            <input type="text" name="facebook[page_name]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Ad Campaign Name</label>
                                            <input type="text" name="facebook[ad_campaign_name]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Post URL</label>
                                            <input type="url" name="facebook[post_url]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Inquiry Message</label>
                                            <textarea name="facebook[inquiry_message]" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <!-- Fiverr -->
                                    <div class="platform-field d-none" id="fiverrFields">
                                        <h5>Fiverr Details</h5>
                                        <div class="form-group">
                                            <label>Gig Title</label>
                                            <input type="text" name="fiverr[gig_title]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Buyer Request Message</label>
                                            <textarea name="fiverr[buyer_request_message]" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Offer Amount</label>
                                            <input type="number" name="fiverr[offer_amount]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Buyer Username</label>
                                            <input type="text" name="fiverr[buyer_username]" class="form-control">
                                        </div>
                                    </div>

                                    <!-- Other -->
                                    <div class="platform-field d-none" id="otherFields">
                                        <h5>Other Platform Details</h5>
                                        <div class="form-group">
                                            <label>Platform Name</label>
                                            <input type="text" name="other[platform_name]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Platform URL</label>
                                            <input type="url" name="other[platform_url]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Campaign Name</label>
                                            <input type="text" name="other[campaign_name]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Inquiry Message</label>
                                            <textarea name="other[inquiry_message]" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Estimated Budget</label>
                                            <input type="number" name="other[estimated_budget]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Contact Method</label>
                                            <input type="text" name="other[contact_method]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="saveLeadBtn">Save Lead</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
</div>
@endsection
