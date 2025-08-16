@include('admin.layouts.head')

@include('admin.layouts.sidebar')
<!-- end left-sidenav-->

@include('admin.layouts.header')
<!-- Top Bar End -->

<!-- Page Content-->
<div class="main">
    @yield('content')
</div>

@include('admin.layouts.footer');
<!-- end page content -->
@include('admin.layouts.script')
