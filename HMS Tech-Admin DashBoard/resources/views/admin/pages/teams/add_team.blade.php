@extends('admin.layouts.main')
@section('title')
Team - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container my-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0 text-white">🚀 Create New Team</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('teams.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Team Name</label>
                    <input type="text" name="name" class="form-control border-primary" placeholder="Enter team name" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Filter By Role</label>
                    <select id="role-filter" class="form-select border-primary w-50">
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
                                <div class="card border border-primary h-100 shadow-sm user-card" style="transition: 0.3s;">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input mt-3" type="checkbox" name="users[]" value="{{ $user->id }}" id="user{{ $user->id }}">
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
                    <i class="bi bi-check-circle me-1"></i> Create Team
                </button>
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
