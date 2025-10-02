@extends('admin.layouts.main')

@section('content')
<div class="container my-5">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0">✏️ Edit Team</h3>
        </div>

        <div class="card-body">
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="{{ route('teams.update', $team) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold">Team Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control border-warning" 
                        placeholder="Enter team name"
                        value="{{ old('name', $team->name) }}"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Filter By Role</label>
                    <select id="role-filter" class="form-select border-warning w-50">
                        <option value="">-- Show All Roles --</option>
                        @php
                            $roles = $users->pluck('role')->unique()->filter()->values();
                        @endphp
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Select Users</label>
                    <div class="row">
                        @foreach($users as $user)
                            <div class="col-md-3 mb-3 user-item" data-role="{{ $user->role }}">
                                <div class="card border border-warning h-100 shadow-sm user-card" style="transition: 0.3s;">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input mt-3" 
                                                type="checkbox" 
                                                name="users[]" 
                                                value="{{ $user->id }}" 
                                                id="user{{ $user->id }}"
                                                {{ $team->users->contains($user->id) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label fw-semibold h4" for="user{{ $user->id }}">
                                                {{ $user->name }}
                                            </label>
                                        </div>
                                        <span class="badge bg-info mt-2 text-white">
                                            {{ $user->role ?? 'No Role' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="bi bi-save me-1"></i> Update Team
                </button>
                <a href="{{ route('teams.index') }}" class="btn btn-secondary px-4 py-2 ms-2">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
</div>

{{-- ✅ Filter users by role --}}
<script>
document.getElementById('role-filter').addEventListener('change', function() {
    let role = this.value;
    let users = document.querySelectorAll('.user-item');
    users.forEach(function(item) {
        if (role === '' || item.getAttribute('data-role') === role) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Hover effect
let cards = document.querySelectorAll('.user-card');
cards.forEach(function(card) {
    card.addEventListener('mouseenter', function() {
        this.classList.add('bg-light');
    });
    card.addEventListener('mouseleave', function() {
        this.classList.remove('bg-light');
    });
});
</script>
@endsection
