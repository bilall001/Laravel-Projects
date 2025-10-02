@extends('admin.layouts.main')

@section('content')
<div class="container my-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-primary mb-0">🔐 Add New Password</h1>
    <a href="{{ route('passwords.index') }}" class="btn bg-black btn-secondary">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <!-- Password Create Form Card -->
  <div class="card border-0 shadow-sm">
    <div class="card-header text-white h4" style="background-color: rgb(2, 2, 100)">
      New Password Details
    </div>
    <div class="card-body">
     <form action="{{ route('passwords.store') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label for="site" class="form-label">Site</label>
    <input type="text" name="site" id="site" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" name="username" id="username" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="text" name="password" id="password" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
  </div>

  <button type="submit" class="btn btn-primary">
    <i class="bi bi-save"></i> Save Password
  </button>
</form>

    </div>
  </div>

</div>
@endsection
