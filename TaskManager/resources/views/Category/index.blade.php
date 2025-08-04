@extends('layouts.app')

@section('css')
<style>
  /* ✔ Success Tick Circle */
  .checkmark-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 6px solid #28a745;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: popIn 0.4s ease-out;
    position: relative;
  }

  .checkmark-circle::after {
    content: '';
    position: absolute;
    width: 30px;
    height: 60px;
    border-right: 6px solid #28a745;
    border-bottom: 6px solid #28a745;
    transform: rotate(45deg);
    top: 15px;
    left: 40px;
  }

  /* ❌ Error Cross Circle */
  .crossmark-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 6px solid #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: popIn 0.4s ease-out;
    position: relative;
  }

  .crossmark-circle::before, .crossmark-circle::after {
    content: '';
    position: absolute;
    width: 60px;
    height: 6px;
    background-color: #dc3545;
    top: 57px;
    left: 30px;
  }

  .crossmark-circle::before {
    transform: rotate(45deg);
  }

  .crossmark-circle::after {
    transform: rotate(-45deg);
  }

  /* Countdown Text */
  #countdown-text {
    font-size: 1.1rem;
    color: #555;
  }

  #countdown {
    font-weight: bold;
    color: #007bff;
    font-size: 1.3rem;
    animation: pulse 1s infinite;
  }

  @keyframes popIn {
    0% {
      transform: scale(0.5);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }
</style>
@endsection

@section('content')
<div class="wrapper">
  <section class="content-header">
    <div class="container-fluid my-2">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>All Categories</h1>
        </div>
        <div class="col-sm-6 text-right">
          <a href="{{ route('category.create') }}" class="btn btn-primary">Add New Category</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <div class="card-tools">
            <div class="input-group input-group" style="width: 250px;">
              <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
              <div class="input-group-append">
                <button type="submit" class="btn btn-default">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th width="60">ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th width="100">Actions</th>
              </tr>
            </thead>
            <tbody>
              @if ($categories && $categories->count())
                @foreach ($categories as $task)
                  <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->slug }}</td>
                    <td>{{ $task->description }}</td>
                    <td>
                      <a href="{{ route('category.edit', $task->id) }}">
                        <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                      </a>
                      <form action="{{ route('category.destroy', $task->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border: none; background-color: transparent;" class="text-danger">
                          <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                          </svg>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="5" class="text-center">No categories found.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div> <!-- /.card-body -->

        <div class="card-footer d-flex justify-content-end">
          {!! $categories->links() !!}
        </div>
      </div> <!-- /.card -->
    </div>
  </section>

  <!-- Modal -->
  <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content text-center p-4">
        <div id="modal-icon" class="mb-3"></div>
        <h4 class="modal-title mb-2" id="feedbackModalLabel">
          {{ session('success') ? 'Success' : (session('error') ? 'Error' : '') }}
        </h4>
        <p class="lead">
          {{ session('success') ?? session('error') }}
        </p>
        <p class="text-muted mt-3" id="countdown-text">Redirecting in <span id="countdown">5</span> seconds...</p>
        <button type="button" class="btn btn-outline-secondary mt-3" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!-- /.wrapper -->
@endsection

@section('scripts')
@if(session('success') || session('error'))
<script>
  $(document).ready(function () {
    let iconHtml = `{!! session('success') ? '<div class="checkmark-circle mx-auto"></div>' : '<div class="crossmark-circle mx-auto"></div>' !!}`;
    $('#modal-icon').html(iconHtml);
    $('#feedbackModal').modal('show');

    let seconds = 5;
    let countdownInterval = setInterval(function () {
      seconds--;
      $('#countdown').text(seconds);
      if (seconds <= 0) {
        clearInterval(countdownInterval);
        window.location.href = "{{ route('category.index') }}";
      }
    }, 1000);
  });
</script>
@endif
@endsection
