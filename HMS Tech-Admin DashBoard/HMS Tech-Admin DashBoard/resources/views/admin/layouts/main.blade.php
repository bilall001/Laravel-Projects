@include('admin.layouts.head')

@include('admin.layouts.sidebar')
<!-- end left-sidenav-->

@include('admin.layouts.header')
<!-- Top Bar End -->

<!-- Page Content-->

<div class="main">
@if(session()->has('impersonator_id'))
  <div class="alert alert-info d-flex justify-content-between align-items-center mb-2">
    <div>
      <strong>Impersonation active:</strong>
      You’re logged in as <em>{{ auth()->user()->name ?? auth()->user()->email }}</em>.
      <small class="ms-2">(debug: admin={{ session('impersonator_id') }})</small> {{-- TEMP --}}
    </div>
    <form id="impersonation-stop" action="{{ route('impersonate.stop') }}" method="POST" class="m-0">
      @csrf
      <button type="submit" class="btn btn-sm btn-dark">Leave Impersonation</button>
    </form>
  </div>
@endif



    @yield('content')
</div>

@include('admin.layouts.footer');
<!-- end page content -->
@include('admin.layouts.script')
