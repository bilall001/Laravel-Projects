@extends('admin.layouts.main')

@section('title', 'Manage Roles for ' . $project->title)

@section('custom_css')
<style>
    .card-header {
        font-weight: 600;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .role-badge {
        background: #17a2b8;
        color: #fff;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
    .developer-badge {
        background: #6c757d;
        color: #fff;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
    .table th {
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">
        <i class="fas fa-user-tag text-primary"></i> Manage Roles – 
        <span class="text-dark">{{ $project->title }}</span>
    </h4>

    {{-- Assign to Developers --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user-plus me-2"></i> Assign Role to Developers
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.projects.roles.assignToDevelopers', $project->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Developers</label>
                        <select name="developer_ids[]" class="form-control select2" multiple required>
                            @foreach($eligibleDevelopers as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Assign to Teams --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <i class="fas fa-users me-2"></i> Assign Role to Teams
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.projects.roles.assignToTeams', $project->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Teams</label>
                        <select name="team_ids[]" class="form-control select2" multiple required>
                            @foreach($project->teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Current Assignments --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-list me-2"></i> Current Role Assignments
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Developer</th>
                        <th>Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberRoles as $mr)
                        <tr>
                            <td>
                                <span class="developer-badge">{{ $mr->developer->user->name }}</span>
                            </td>
                            <td>
                                <span class="role-badge">{{ $mr->role->name }}</span>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.projects.roles.revokeFromDevelopers', $project->id) }}">
                                    @csrf
                                    <input type="hidden" name="role_id" value="{{ $mr->role_id }}">
                                    <input type="hidden" name="developer_ids[]" value="{{ $mr->developer_id }}">
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Revoke this role?')">
                                        <i class="fas fa-times-circle"></i> Revoke
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No roles assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('custom_js')
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%', placeholder: "Select options" });
    });
</script>
@endpush
