@extends('admin.layouts.main')

@section('content')
<div class="container my-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-primary mb-0">🔐 View Password</h1>
    <a href="{{ route('passwords.index') }}" class="btn btn-white bg-black">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <!-- Password Details Card -->
  <div class="card border-0 shadow-sm">
    <div class="card-header text-white h4" style="background-color: rgb(2, 2, 100)">
      Password Details
    </div>
    <div class="card-body">
      <div class="mb-3">
        <h5 class="mb-1 text-secondary">Site:</h5>
        <p class="h5">{{ $data['site'] }}</p>
      </div>
      <div class="mb-3">
        <h5 class="mb-1 text-muted">Username:</h5>
        <p class="h5">{{ $data['username'] }}</p>
      </div>
      <div class="mb-3">
        <h5 class="mb-1 text-muted">Password:</h5>
        <p class="h5">{{ $data['password'] }}</p>
      </div>
      @if(!empty($data['description']))
  <p><strong>Description:</strong> {{ $data['description'] }}</p>
@endif
    </div>
  </div>

</div>
@endsection
