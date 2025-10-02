@extends('admin.layouts.main')
@section('title', 'My Profile')
@section('content')

<div class="container py-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="bi bi-person-circle me-2 text-primary"></i> My Profile
        </h2>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Profile Info --}}
        <div class="col-lg-12 mb-4">
            <div class="card shadow col-lg-6 border-0 h-100">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
                    <i class="bi bi-person-lines-fill me-2"></i> Update Profile
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Full Name</label>
                            <input type="text" name="name" value="{{ old('name',$user->name) }}"
                                   class="form-control" placeholder="Enter your full name">
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Email Address</label>
                            <input type="email" name="email" value="{{ old('email',$user->email) }}"
                                   class="form-control" placeholder="Enter your email">
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Password Change --}}
        <div class="col-lg-12 mb-4 justify-center">
            <div class="card shadow col-lg-6 border-0 h-100">
                <div class="card-header bg-gradient-dark text-white d-flex align-items-center">
                    <i class="bi bi-shield-lock-fill me-2"></i> Change Password
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control"
                                   placeholder="Enter current password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-semibold">New Password(Min 8)</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Enter new password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Confirm new password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
