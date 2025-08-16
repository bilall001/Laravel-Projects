@extends('admin.layouts.main')

@section('content')
<div class="container my-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-primary mb-0">🔐 All Passwords</h1>
    <a href="{{ route('passwords.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New
    </a>
  </div>

  <!-- Success Alert -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Passwords Table Card -->
  <div class="card border-0 shadow-sm">
    <div class="card-header text-white h3" style="background-color: rgb(2, 2, 100)">
      Password List
    </div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead class="table-primary h4">
          <tr>
            <th>Site</th>
            <th>Username</th>
            <th>Description</th>

            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($passwords as $password)
          <tr>
            <td>{{ $password->data['site'] }}</td>
            <td>{{ $password->data['username'] }}</td>
            <td>{{ $password->data['description'] ?? '-' }}</td>

            <td>
              <div class="btn-group " role="group">
                <!-- View Button -->
                <a href="{{ route('passwords.show', $password->id) }}" 
                   class="btn btn-sm btn-outline-warning bg-warning text-white" 
                   title="View" >
                   
                  <i class="bi bi-eye"></i> View
                </a>

                <!-- Edit Button -->
                <a href="{{ route('passwords.edit', $password->id) }}" 
                   class="btn btn-sm btn-outline-primary bg-primary ms-3 text-white" 
                   title="Edit" style="margin-left: 5px">
                  <i class="bi bi-pencil"></i> Edit
                </a>

                <!-- Delete Button -->
                <form action="{{ route('passwords.destroy', $password->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this password?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger bg-danger ms-3 text-white" title="Delete" style="margin-left: 5px">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center text-muted py-4">No passwords found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
