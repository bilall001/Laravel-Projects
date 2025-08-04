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
  .crossmark-circle::before,
  .crossmark-circle::after {
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

  /* ⏱ Countdown Text */
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

  /* 🔄 Pop-in Animation */
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

  /* 🔁 Countdown Pulse Animation */
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
          <h1>Edit Category</h1>
        </div>
        <div class="col-sm-6 text-right">
          <a href="{{ route('category.index') }}" class="btn btn-primary">Back</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
          <div class="card-body">
            <div class="row">
              <!-- Title -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name">Category</label>
                  <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="slug">Slug</label>
                  <input type="text" name="slug" id="slug" class="form-control" value="{{ $category->slug }}" required>
                </div>
              </div>

              <!-- Description -->
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="description">Description</label>
                  <textarea name="description" id="description" class="form-control" rows="5">{{ $category->description }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <div class="pb-5 pt-3">
          <button type="submit" class="btn btn-success">Update Category</button>
          <a href="{{ route('category.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
      </form>

      <!-- Feedback Modal -->
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
    </div>
  </section>
</div>
@endsection

@section('scripts')
@if(session('success') || session('error'))
<script>
  $(document).ready(function () {
    let iconHtml = `{!! session('success') 
        ? '<div class="checkmark-circle mx-auto"></div>' 
        : '<div class="crossmark-circle mx-auto"></div>' 
    !!}`;
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
