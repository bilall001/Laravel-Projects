@extends('admin.layouts.main')
@section('title', 'All Developer Submissions - HMS Tech & Solutions')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📊 Developer Submissions & Points</h3>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0 text-white">🔎 Filter Submissions</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.developer.points') }}" class="form-row">

                {{-- Developer --}}
                <div class="col-md-3 mb-3">
                    <label class="small fw-bold">Developer</label>
                    <select name="developer_id" class="form-control">
                        <option value="">All Developers</option>
                        @foreach($developers as $dev)
                            <option value="{{ $dev->id }}" {{ request('developer_id') == $dev->id ? 'selected' : '' }}>
                                {{ $dev->user->name ?? 'Dev #'.$dev->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Project --}}
                <div class="col-md-3 mb-3">
                    <label class="small fw-bold">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range --}}
                <div class="col-md-3 mb-3">
                    <label class="small fw-bold">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="small fw-bold">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-md-3 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-success btn-block">
                        🔍 Apply Filters
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end mb-3">
                    <a href="{{ route('admin.developer.points') }}" class="btn btn-secondary btn-block">
                        ♻ Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Submissions Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0 text-white">📜 Submissions List</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="thead-light bg-dark">
                    <tr>
                        <th>Developer</th>
                        <th>Project</th>
                        <th>Team</th>
                        <th>GitHub</th>
                        <th>Video</th>
                        <th>Uploaded At</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($points as $point)
                        <tr>
                            {{-- Developer --}}
                            <td>
                                <strong style="text-transform: capitalize;">{{ $point->developer->user->name ?? 'Unknown' }}</strong>
                            </td>

                            {{-- Project --}}
                            <td>{{ $point->project->title ?? '-' }}</td>

                            {{-- Team --}}
                            <td>{{ $point->team->name ?? '-' }}</td>

                            {{-- GitHub --}}
                            <td>
                                @if($point->github_url)
                                    <a href="{{ $point->github_url }}" target="_blank" class="btn btn-sm btn-outline-dark">🐙 Repo</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            {{-- Video --}}
                            <td>
                                @if($point->video_link)
                                    <a href="{{ $point->video_link }}" target="_blank" class="btn btn-sm btn-outline-info">🔗 Link</a>
                                @elseif($point->video_file)
                                    <a href="{{ asset('storage/'.$point->video_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">📁 File</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td>{{ \Carbon\Carbon::parse($point->uploaded_at)->format('d M Y, h:i A') }}</td>

                            {{-- Points --}}
                            <td>
                                <span class="badge {{ $point->points >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $point->points }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $points->links() }} {{-- ✅ Laravel pagination --}}
        </div>
    </div>
</div>
@endsection
