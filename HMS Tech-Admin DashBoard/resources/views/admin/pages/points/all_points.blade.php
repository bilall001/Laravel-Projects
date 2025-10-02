@extends('admin.layouts.main')
@section('title')
Developer Points - HMS Tech & Solutions
@endsection

@section('content')
<div class="container mt-4">

    {{-- ✅ Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ❌ Error Alert --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- My Projects --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white">📂 My Projects</div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project Title</th>
                        <th>Project File</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->title }}</td>
                            <td>
                                @if($project->file)
                                    <a href="{{ asset('storage/'.$project->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">📄 View File</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $project->end_date ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white">📤 Upload Submission</div>
        <div class="card-body">
            <form action="{{ route('developer.points.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Select Project --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Team (Optional) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Team (Optional)</label>
                    <select name="team_id" class="form-select">
                        <option value="">-- No Team --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Video Link --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Video Link</label>
                    <input type="url" name="video_link" class="form-control" placeholder="https://">
                </div>

                {{-- Video File --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Or Upload Video File</label>
                    <input type="file" name="video_file" class="form-control">
                </div>
                {{-- github url --}}
                <div class="mb-3">
    <label class="form-label fw-bold">GitHub URL (Optional)</label>
    <input type="url" name="github_url" class="form-control" placeholder="https://github.com/username/repo">
</div>
                <button class="btn btn-primary">🚀 Submit</button>
            </form>
        </div>
    </div>
    @foreach($points as $point)
<div class="modal fade" id="editModal{{ $point->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $point->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="editModalLabel{{ $point->id }}">✏ Edit Submission</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('developer.points.update', $point->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-body">

          <div class="mb-3">
              <label class="form-label fw-bold">Video Link</label>
              <input type="url" name="video_link" value="{{ $point->video_link }}" class="form-control" placeholder="https://">
          </div>

          <div class="mb-3">
              <label class="form-label fw-bold">Upload New Video (Optional)</label>
              <input type="file" name="video_file" class="form-control">
              @if($point->video_file)
                  <small class="text-muted">Current: <a href="{{ asset('storage/'.$point->video_file) }}" target="_blank">View</a></small>
              @endif
          </div>

          <div class="mb-3">
              <label class="form-label fw-bold">GitHub URL</label>
              <input type="url" name="github_url" value="{{ $point->github_url }}" class="form-control" placeholder="https://github.com/username/repo">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">❌ Cancel</button>
          <button type="submit" class="btn btn-success">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

    {{-- Submitted Points History --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">📜 My Submitted Points</div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th>Team</th>
                        <th>Link/File</th>
                        <th>GitHub</th>
                        <th>Uploaded At</th>
                        <th>Points</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($points as $point)
                        <tr>
                            <td>{{ $point->project->title ?? '-' }}</td>
                            <td>{{ $point->team->name ?? '-' }}</td>
                            <td>
                                @if($point->video_link)
                                    <a href="{{ $point->video_link }}" target="_blank" class="btn btn-sm btn-outline-info">🔗 Link</a>
                                @elseif($point->video_file)
                                    <a href="{{ asset('storage/'.$point->video_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">📁 File</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
    @if($point->github_url)
        <a href="{{ $point->github_url }}" target="_blank" class="btn btn-sm btn-outline-dark">🐙 GitHub</a>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>
                            <td>{{ $point->uploaded_at }}</td>
                            <td>
                                <span class="badge {{ $point->points >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $point->points }}
                                </span>
                            </td>
                          <td>
    @php
        $endDate = $point->project->developer_end_date ?? $point->project->end_date;
    @endphp

    @if(!$endDate || \Carbon\Carbon::today()->lte(\Carbon\Carbon::parse($endDate)))
        <!-- Edit Icon -->
        <a href="#" data-toggle="modal" data-target="#editModal{{ $point->id }}">
            <i class="fas fa-edit text-info" ></i>
        </a>
    @endif

    <!-- Delete Icon -->
    <form action="{{ route('developer.points.destroy', $point->id) }}" method="POST" onsubmit="return confirm('Delete this submission?')">
        @csrf @method('DELETE')
        <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </form>
</td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
