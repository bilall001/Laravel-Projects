@extends('admin.layouts.main')

@section('title')
    Leads - HMS Tech & Solutions
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">👥 Manage Leads</h4>
            @if (auth()->user()->role === 'admin' ||
                    auth()->user()->role === 'business developer' ||
                    auth()->user()->role === 'team manager')
                <button class="btn btn-primary" data-toggle="modal" data-target="#leadModal">
                    <i class="bi bi-plus-circle"></i> Add Lead
                </button>
            @endif

        </div>


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header text-white" style="background-color: #1D2C48">All Leads</div>
            <div class="card-body table-responsive">
                <table class="table  table-sm text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Lead Title</th>
                            <th>Status</th>
                            <th>Lead Source</th>
                            <th>Expected Budget</th>
                            <th>Expected Start Date</th>
                            <th>Next Follow Up</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leads as $index => $lead)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lead->lead_title ?? 'N/A' }}</td>
                                <td>{{ ucfirst($lead->status) }}</td>
                                <td>{{ ucfirst($lead->lead_get_by) }}</td>
                                <td>${{ $lead->expected_budget ?? 'N/A' }}</td>
                                <td>{{ $lead->expected_start_date ?? 'N/A' }}</td>
                                <td>{{ $lead->next_follow_up ?? 'N/A' }}</td>
                                <td>
                                    {{-- View Button --}}
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#leadShowModal{{ $lead->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'business developer' || auth()->user()->role === 'team manager')
                                    {{-- Edit Button --}}
                                    <button class="btn btn-sm btn-outline-primary edit-lead-btn"
                                        data-id="{{ $lead->id }}" data-url="{{ route('leads.update', $lead->id) }}"
                                        data-title="{{ $lead->lead_title }}"
                                        data-description="{{ $lead->lead_description }}" data-status="{{ $lead->status }}"
                                        data-source="{{ $lead->lead_get_by }}" data-budget="{{ $lead->expected_budget }}"
                                        data-start="{{ $lead->expected_start_date }}"
                                        data-person="{{ $lead->contact_person }}" data-email="{{ $lead->contact_email }}"
                                        data-phone="{{ $lead->contact_phone }}" data-follow="{{ $lead->next_follow_up }}"
                                        data-notes="{{ $lead->notes }}" data-platform='@json($lead->{$lead->lead_get_by} ?? [])'
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Delete Form --}}
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14">No leads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Leads Create/Edit Modal -->
        <div class="modal fade" id="leadModal" tabindex="-1" role="dialog" aria-labelledby="leadModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <form id="leadForm" method="POST" action="{{ route('leads.store') }}">
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
            </div>
        </div>

        {{-- show modal --}}
        {{-- show modal --}}
        {{-- Enhanced Lead Show Modal --}}
        @foreach ($leads as $lead)
            <div class="modal fade" id="leadShowModal{{ $lead->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0 rounded-3">
                        <div class="modal-header bg-primary text-white rounded-top">
                            <h5 class="modal-title fw-bold"><i class="fas fa-user"></i> Lead Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4" style="font-family: 'Poppins', sans-serif;">
                            {{-- General Lead Info --}}
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header bg-light fw-bold">General Information</div>
                                <div class="card-body row">
                                    <div class="col-md-4"><strong>📌 Title:</strong> {{ $lead->lead_title ?? '-' }}</div>
                                    <div class="col-md-4"><strong>📊 Status:</strong> {{ ucfirst($lead->status) ?? '-' }}
                                    </div>
                                    <div class="col-md-4"><strong>📥 Source:</strong>
                                        {{ ucfirst($lead->lead_get_by) ?? '-' }}</div>
                                    <div class="col-md-4"><strong>💲 Expected Budget:</strong>
                                        ${{ $lead->expected_budget ?? '-' }}</div>
                                    <div class="col-md-4"><strong>📅 Expected Start:</strong>
                                        {{ $lead->expected_start_date ?? '-' }}</div>
                                    <div class="col-md-4"><strong>👤 Contact Person:</strong>
                                        {{ $lead->contact_person ?? '-' }}</div>
                                    <div class="col-md-4"><strong>📧 Email:</strong> {{ $lead->contact_email ?? '-' }}
                                    </div>
                                    <div class="col-md-4"><strong>📞 Phone:</strong> {{ $lead->contact_phone ?? '-' }}
                                    </div>
                                    <div class="col-md-4"><strong>📅 Next Follow Up:</strong>
                                        {{ $lead->next_follow_up ?? '-' }}</div>
                                    <div class="col-12"><strong>📝 Notes:</strong> {{ $lead->notes ?? '-' }}</div>
                                </div>
                            </div>

                            {{-- Platform Details --}}
                            @if ($lead->lead_get_by && $lead->{$lead->lead_get_by})
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light fw-bold">
                                        @if ($lead->lead_get_by === 'linkedin')
                                            🔗 LinkedIn Details
                                        @elseif($lead->lead_get_by === 'upwork')
                                            🖥 Upwork Details
                                        @elseif($lead->lead_get_by === 'facebook')
                                            📘 Facebook Details
                                        @elseif($lead->lead_get_by === 'fiverr')
                                            🎯 Fiverr Details
                                        @elseif($lead->lead_get_by === 'other')
                                            🔧 Other Platform Details
                                        @endif
                                    </div>
                                    <div class="card-body row">
                                        @php $platform = $lead->{$lead->lead_get_by}; @endphp

                                        @if ($lead->lead_get_by === 'linkedin')
                                            <div class="col-md-6"><strong>Company Name:</strong>
                                                {{ $platform->company_name ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Profile Link:</strong> <a
                                                    href="{{ $platform->profile_link ?? '#' }}"
                                                    target="_blank">{{ $platform->profile_link ?? '-' }}</a></div>
                                            <div class="col-md-6"><strong>Job Post URL:</strong> <a
                                                    href="{{ $platform->job_post_url ?? '#' }}"
                                                    target="_blank">{{ $platform->job_post_url ?? '-' }}</a></div>
                                            <div class="col-md-6"><strong>Message Sent:</strong>
                                                {{ $platform->message_sent ?? '-' }}</div>
                                        @elseif($lead->lead_get_by === 'upwork')
                                            <div class="col-md-6"><strong>Project Title:</strong>
                                                {{ $platform->project_title ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Job URL:</strong> <a
                                                    href="{{ $platform->job_url ?? '#' }}"
                                                    target="_blank">{{ $platform->job_url ?? '-' }}</a></div>
                                            <div class="col-md-6"><strong>Proposal:</strong>
                                                {{ $platform->proposal_cover_letter ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Connect Bids:</strong>
                                                {{ $platform->connect_bids ?? '-' }}</div>
                                        @elseif($lead->lead_get_by === 'facebook')
                                            <div class="col-md-6"><strong>Page Name:</strong>
                                                {{ $platform->page_name ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Ad Campaign Name:</strong>
                                                {{ $platform->ad_campaign_name ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Post URL:</strong> <a
                                                    href="{{ $platform->post_url ?? '#' }}"
                                                    target="_blank">{{ $platform->post_url ?? '-' }}</a></div>
                                            <div class="col-12"><strong>Inquiry Message:</strong>
                                                {{ $platform->inquiry_message ?? '-' }}</div>
                                        @elseif($lead->lead_get_by === 'fiverr')
                                            <div class="col-md-6"><strong>Gig Title:</strong>
                                                {{ $platform->gig_title ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Buyer Username:</strong>
                                                {{ $platform->buyer_username ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Offer Amount:</strong>
                                                {{ $platform->offer_amount ?? '-' }}</div>
                                            <div class="col-12"><strong>Buyer Request:</strong>
                                                {{ $platform->buyer_request_message ?? '-' }}</div>
                                        @elseif($lead->lead_get_by === 'other')
                                            <div class="col-md-6"><strong>Platform Name:</strong>
                                                {{ $platform->platform_name ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Platform URL:</strong> <a
                                                    href="{{ $platform->platform_url ?? '#' }}"
                                                    target="_blank">{{ $platform->platform_url ?? '-' }}</a></div>
                                            <div class="col-md-6"><strong>Campaign Name:</strong>
                                                {{ $platform->campaign_name ?? '-' }}</div>
                                            <div class="col-md-6"><strong>Estimated Budget:</strong>
                                                {{ $platform->estimated_budget ?? '-' }}</div>
                                            <div class="col-12"><strong>Inquiry Message:</strong>
                                                {{ $platform->inquiry_message ?? '-' }}</div>
                                            <div class="col-12"><strong>Contact Method:</strong>
                                                {{ $platform->contact_method ?? '-' }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer bg-light rounded-bottom">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach



    </div>
    {{-- show modal script --}}
    <!-- JS to Handle Platform-Specific Fields -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const leadGetBy = document.getElementById("lead_get_by");
            const platformFields = document.querySelectorAll(".platform-field");

            function toggleFields() {
                platformFields.forEach(f => f.classList.add("d-none"));
                const selected = leadGetBy.value;
                if (selected) {
                    const activeSection = document.getElementById(selected + "Fields");
                    if (activeSection) activeSection.classList.remove("d-none");
                }
            }

            leadGetBy.addEventListener("change", toggleFields);
            toggleFields();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const leadGetBy = document.getElementById("lead_get_by");
            const platformFields = document.querySelectorAll(".platform-field");

            function toggleFields() {
                platformFields.forEach(f => {
                    f.classList.add("d-none");
                    // Clear all inputs in hidden fields
                    f.querySelectorAll('input, textarea').forEach(el => el.value = '');
                });

                const selected = leadGetBy.value;
                if (selected) {
                    const activeSection = document.getElementById(selected + "Fields");
                    if (activeSection) activeSection.classList.remove("d-none");
                }
            }

            leadGetBy.addEventListener("change", toggleFields);

            // EDIT LEAD BUTTON LOGIC
            document.querySelectorAll(".edit-lead-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let modal = document.getElementById("leadModal");
                    let form = modal.querySelector("form");

                    // Ensure _method input exists for PUT
                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set form action URL
                    form.action = this.dataset.url;

                    // Fill basic fields
                    modal.querySelector("[name='lead_title']").value = this.dataset.title;
                    modal.querySelector("[name='lead_description']").value = this.dataset
                        .description;
                    modal.querySelector("[name='status']").value = this.dataset.status;
                    modal.querySelector("[name='lead_get_by']").value = this.dataset.source;
                    modal.querySelector("[name='expected_budget']").value = this.dataset.budget;
                    modal.querySelector("[name='expected_start_date']").value = this.dataset.start;
                    modal.querySelector("[name='contact_person']").value = this.dataset.person;
                    modal.querySelector("[name='contact_email']").value = this.dataset.email;
                    modal.querySelector("[name='contact_phone']").value = this.dataset.phone;
                    modal.querySelector("[name='next_follow_up']").value = this.dataset.follow;
                    modal.querySelector("[name='notes']").value = this.dataset.notes;

                    // Show only relevant platform section
                    toggleFields();

                    // Prefill platform-specific fields if data exists
                    const platform = this.dataset.source;
                    if (platform) {
                        const platformData = JSON.parse(this.dataset.platform || '{}');
                        const section = document.getElementById(platform + "Fields");
                        if (section) {
                            Object.keys(platformData).forEach(key => {
                                const field = section.querySelector("[name='" + platform +
                                    "[" + key + "]']");
                                if (field) field.value = platformData[key];
                            });
                        }
                    }

                    // Show modal
                    $('#leadModal').modal('show');
                });
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const leadType = document.getElementById('lead_type');
            const wrapper = document.getElementById('projectClientWrapper');

            function toggleFields() {
                if (leadType.value === 'existing') {
                    wrapper.style.display = 'flex'; // show
                } else {
                    wrapper.style.display = 'none'; // hide
                    // clear values when hiding
                    document.getElementById('project_id').value = '';
                    document.getElementById('client_id').value = '';
                }
            }

            leadType.addEventListener('change', toggleFields);

            // run once on page load (for edit form)
            toggleFields();
        });
    </script>
@endsection
