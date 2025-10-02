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
                    @forelse($developer->projects as $project)
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
                        <tr><td colspan="3" class="text-center text-muted">No projects found.</td></tr>
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

                <div class="mb-3">
                    <label class="form-label fw-bold">Select Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Video Link</label>
                    <input type="url" name="video_link" class="form-control" placeholder="https://">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Or Upload Video File</label>
                    <input type="file" name="video_file" class="form-control">
                </div>

                <button class="btn btn-primary">🚀 Submit</button>
            </form>
        </div>
    </div>

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
                            <td>{{ $point->uploaded_at }}</td>
                            <td>
                                <span class="badge {{ $point->points >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $point->points }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('developer.points.destroy', $point->id) }}" method="POST" onsubmit="return confirm('Delete this submission?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
