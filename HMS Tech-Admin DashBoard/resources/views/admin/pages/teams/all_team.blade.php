@extends('admin.layouts.main')
@section('title')
Team - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container my-5">

  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0 text-white">📋 All Teams</h3>
      <a href="{{ route('teams.create') }}" class="btn btn-light text-primary fw-bold">
        <i class="bi bi-plus-circle me-1"></i> Create Team
      </a>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-primary h5">
            <tr>
              <th scope="col">Team Name</th>
              <th scope="col">Users</th>
              <th scope="col" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($teams as $team)
              <tr>
                <td class="fw-bold">{{ $team->name }}</td>
                <td>
                  @foreach($team->users as $user)
                    <span class="badge bg-black me-1 text-white">{{ $user->name }}</span>
                  @endforeach
                </td>
                <td class="text-center">
                  <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <form action="{{ route('teams.destroy', $team) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">No teams found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
