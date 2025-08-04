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
			<!-- Content Wrapper. Contains page content -->
			
				<!-- Content Header (Page header) -->
				<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Task</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href={{route('tasks.index')}} class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
    <div class="container-fluid">
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title">Task Title</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Enter task title" required>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id">Category</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id }}">{{$category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- User -->
                         <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id">Assign to User</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status1" id="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority">Priority</label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="attachment">Attachment (optional)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control-file">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Task details"></textarea>
                            </div>
                        </div>
                    </div>
                </div>							
            </div>

			<!-- Success Modal -->


            <!-- Submit -->
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
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

				<!-- /.content -->			
		</div>
        @endsection
@section('scripts')
@if(session('success') || session('error'))
<script>
    $(document).ready(function () {
        // Show tick or cross
        let iconHtml = `
            {!! session('success') 
                ? '<div class="checkmark-circle mx-auto"></div>' 
                : '<div class="crossmark-circle mx-auto"></div>' 
            !!}
        `;
        $('#modal-icon').html(iconHtml);

        // Show the modal
        $('#feedbackModal').modal('show');

        // Countdown and redirect
        let seconds = 5;
        let countdownInterval = setInterval(function () {
            seconds--;
            $('#countdown').text(seconds);
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "{{ route('tasks.index') }}"; // Change route if needed
            }
        }, 1000);
    });
</script>
@endif
@endsection
