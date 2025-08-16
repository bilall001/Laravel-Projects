@extends('admin.layouts.main')
@section('title')
Team Manager Dashboard - HMS Tech  & Solutions
@endsection
@section('custom_css')z
<style>
    #ana_dash_1 {
        padding-top: 20px;
    }

    .apexcharts-legend-series {
        cursor: pointer !important;
    }
    [data-lucide] {
    width: 18px;
    height: 18px;
    stroke: #8c9eff;
    margin-right: 8px;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">Analytics</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                            <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                            <span id="Select_date">{{ now()->format('M d') }}</span>
                            <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i data-feather="download" class="align-self-center icon-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 1 --}}
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm text-center p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Number of Clients</h5>
                    <i data-feather="users"></i>
                </div>
                <h2 class="my-3">{{ $clients }}</h2>
                <span class="text-success">+8.5%</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm text-center p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Total Projects</h5>
                    <i data-feather="clock"></i>
                </div>
                <h2 class="my-3">{{ $projects }}</h2>
                <span class="text-success">+1.5%</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm text-center p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Projects</h5>
                    <i data-feather="activity"></i>
                </div>
                <h2 class="my-3">{{ $currentProjects }}</h2>
                <span class="text-danger">-35%</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm text-center p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Month Completed Projects</h5>
                    <i data-feather="briefcase"></i>
                </div>
                <h2 class="my-3">{{ $monthCompletedProjects }}</h2>
            </div>
        </div>
    </div>
       
    </div>
</div>

    
@endsection
@section('custom_js')

@endsection