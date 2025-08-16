@extends('admin.layouts.main')
@section('title')
Admin - HMS tech  & Solutions
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
        <!-- Page-Title -->
        
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col">
                            <h4 class="page-title">Analytics</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!--end col-->
                        <div class="col-auto align-self-center">
                            <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                                <span class="" id="Select_date">Jan 11</span>
                                <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i data-feather="download" class="align-self-center icon-xs"></i>
                            </a>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end page-title-box-->
            </div><!--end col-->
        </div><!--end row-->
        <!-- end page title end breadcrumb -->
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Number of Clients</p>
                                <h3 class="my-2">{{ $clients }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>8.5%</span> </p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="users" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
            <div class="col-md-6 col-lg-3">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Total Projects</p>
                                <h3 class="my-2">{{ $projects }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>1.5%</span> Weekly Avg.Sessions</p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="clock" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
            <div class="col-md-6 col-lg-3">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Current Projects</p>
                                <h3 class="my-2">{{ $currentProjects }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-danger"><i
                                            class="mdi mdi-trending-down"></i>35%</span> Bounce Rate Weekly</p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="activity" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
            <div class="col-md-6 col-lg-3">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Month Completed Projects</p>
                                <h3 class="my-2">{{ $monthCompletedProjects }}</h3>
                                {{-- <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>10.5%</span> Completions Weekly</p> --}}
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="briefcase" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

        {{-- start second card row --}}
         <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Total Income</p>
                                <h3 class="my-2">{{ $totalIncome }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>8.5%</span> </p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="users" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
            <div class="col-md-6 col-lg-4">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Monthly Expense</p>
                                <h3 class="my-2">{{ $monthExpense }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>1.5%</span> Weekly Avg.Sessions</p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="clock" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
            <div class="col-md-6 col-lg-4">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Monthly Profit</p>
                                <h3 class="my-2">{{ $monthProfit }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-danger"><i
                                            class="mdi mdi-trending-down"></i>35%</span> Bounce Rate Weekly</p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="activity" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> 
            
            <!--end col-->
            {{-- <div class="col-md-6 col-lg-3">
                <div class="card report-card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <p class="text-dark mb-1 font-weight-semibold">Month Completed Projects</p>
                                <h3 class="my-2">{{ $monthCompletedProjects }}</h3>
                                <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                            class="mdi mdi-trending-up"></i>10.5%</span> Completions Weekly</p>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="report-main-icon bg-light-alt">
                                    <i data-feather="briefcase" class="align-self-center text-muted icon-md"></i>
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col--> --}}
        </div>
        {{-- end second card row --}}
        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Monthly Overview</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        This Year<i class="las la-angle-down ml-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Today</a>
                                        <a class="dropdown-item" href="#">Last Week</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">This Year</a>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <div class="">
                            <div id="ana_dash_2" class="apex-charts"></div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
           <!-- Monthly Profit Card -->
<div class="col-lg-3">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Monthly Profit</h4>
                </div>
                <div class="col-auto">
                    <select id="monthSelector" class="form-control form-control-sm">
                        @foreach ($monthlyData as $index => $data)
                            <option value="{{ $index }}">{{ $data['month'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="my-4">
                <div id="monthlyProfitChart" class="apex-charts" style="max-width: 200px; margin: auto;"></div>
                <hr class="hr-dashed w-25 mt-0">
            </div>
            <div class="text-center">
                <h4 id="profitText">{{ $monthlyData[0]['profit_percentage'] }}% Profit</h4>
                <p class="text-muted mt-2">Monthly profit based on income & expense</p>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 mt-2">More details</button>
            </div>
        </div>
    </div>
</div>
 <!--end col-->
        </div><!--end row-->
        <div class="row">


            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Organic Traffic in USA</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Today<i class="las la-angle-down ml-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Today</a>
                                        <a class="dropdown-item" href="#">Yesterday</a>
                                        <a class="dropdown-item" href="#">Last Week</a>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-7">
                                <div id="usa" class="" style="height: 265px"></div>
                            </div><!--end col-->
                            <div class="col-lg-5 align-self-center">
                                <div class="">
                                    <span class="text-dark">Texas</span>
                                    <small class="float-right text-muted ml-3 font-11">81%</small>
                                    <div class="progress mt-2" style="height:3px;">
                                        <div class="progress-bar bg-warning-50" role="progressbar"
                                            style="width: 81%; border-radius:5px;" aria-valuenow="81" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <span class="text-dark">Washington</span>
                                    <small class="float-right text-muted ml-3 font-11">68%</small>
                                    <div class="progress mt-2" style="height:3px;">
                                        <div class="progress-bar bg-warning-50" role="progressbar"
                                            style="width: 68%; border-radius:5px;" aria-valuenow="68" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-dark">Wyoming</span>
                                    <small class="float-right text-muted ml-3 font-11">48%</small>
                                    <div class="progress mt-2" style="height:3px;">
                                        <div class="progress-bar bg-warning-50" role="progressbar"
                                            style="width: 48%; border-radius:5px;" aria-valuenow="48" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <span class="text-dark">Virginia</span>
                                    <small class="float-right text-muted ml-3 font-11">32%</small>
                                    <div class="progress mt-2" style="height:3px;">
                                        <div class="progress-bar bg-warning-50" role="progressbar"
                                            style="width: 32%; border-radius:5px;" aria-valuenow="32" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-body-->
            </div> <!--end col-->
             <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Sessions Device</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-horizontal"
                                            class="align-self-center text-muted icon-xs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Purchases</a>
                                        <a class="dropdown-item" href="#">Emails</a>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <div class="text-center">
                            <div id="ana_device" class="apex-charts"></div>
                            <h6 class="text-primary bg-soft-primary p-3 mb-0">
                                <i data-feather="calendar" class="align-self-center icon-xs mr-1"></i>
                                01 January 2020 to 31 June 2020
                            </h6>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> 
            {{-- <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Browser Used & Traffic Reports</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <ul class="nav nav-pills-custom nav-pills mb-0" id="pills-tab" role="tablist">
                                    <li class="nav-item mr-1">
                                        <a class="nav-link active" id="pills-traffic-tab" data-toggle="pill"
                                            href="#Traffic_Sources" role="tab" aria-controls="pills-traffic"
                                            aria-selected="true">Traffic Sources</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-browser-tab" data-toggle="pill"
                                            href="#Browser_Used" role="tab" aria-controls="pills-browser"
                                            aria-selected="false">Browser Used</a>
                                    </li>
                                </ul>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="Traffic_Sources" role="tabpanel"
                                aria-labelledby="pills-traffic-tab">
                                <div class="table-responsive browser_users">
                                    <table class="table mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="border-top-0">Channel</th>
                                                <th class="border-top-0">Sessions</th>
                                                <th class="border-top-0">Prev.Period</th>
                                                <th class="border-top-0">% Change</th>
                                            </tr><!--end tr-->
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="" class="text-primary">Organic search</a></td>
                                                <td>10853<small class="text-muted">(52%)</small></td>
                                                <td>566<small class="text-muted">(92%)</small></td>
                                                <td> 52.80% <i class="fas fa-caret-up text-success font-16"></i></td>
                                            </tr><!--end tr-->
                                            <tr>
                                                <td><a href="" class="text-primary">Direct</a></td>
                                                <td>2545<small class="text-muted">(47%)</small></td>
                                                <td>498<small class="text-muted">(81%)</small></td>
                                                <td> -17.20% <i class="fas fa-caret-down text-danger font-16"></i></td>

                                            </tr><!--end tr-->
                                            <tr>
                                                <td><a href="" class="text-primary">Referal</a></td>
                                                <td>1836<small class="text-muted">(38%)</small></td>
                                                <td>455<small class="text-muted">(74%)</small></td>
                                                <td> 41.12% <i class="fas fa-caret-up text-success font-16"></i></td>

                                            </tr><!--end tr-->
                                            <tr>
                                                <td><a href="" class="text-primary">Email</a></td>
                                                <td>1958<small class="text-muted">(31%)</small></td>
                                                <td>361<small class="text-muted">(61%)</small></td>
                                                <td> -8.24% <i class="fas fa-caret-down text-danger font-16"></i></td>
                                            </tr><!--end tr-->
                                            <tr>
                                                <td><a href="" class="text-primary">Social</a></td>
                                                <td>1566<small class="text-muted">(26%)</small></td>
                                                <td>299<small class="text-muted">(49%)</small></td>
                                                <td> 29.33% <i class="fas fa-caret-up text-success"></i></td>
                                            </tr><!--end tr-->
                                        </tbody>
                                    </table> <!--end table-->
                                </div><!--end /div-->
                            </div>
                            <div class="tab-pane fade" id="Browser_Used" role="tabpanel"
                                aria-labelledby="pills-browser-tab">
                                <div class="table-responsive browser_users">
                                    <table class="table mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="border-top-0">Browser</th>
                                                <th class="border-top-0">Sessions</th>
                                                <th class="border-top-0">Bounce Rate</th>
                                                <th class="border-top-0">Transactions</th>
                                            </tr><!--end tr-->
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><img src="assets/images/browser_logo/chrome.png" alt=""
                                                        height="24" class="mr-2">Chrome</td>
                                                <td>10853<small class="text-muted">(52%)</small></td>
                                                <td> 52.80%</td>
                                                <td>566<small class="text-muted">(92%)</small></td>
                                            </tr><!--end tr-->
                                            <tr>
                                                <td><img src="assets/images/browser_logo/micro-edge.png" alt=""
                                                        height="24" class="mr-2">Microsoft Edge</td>
                                                <td>2545<small class="text-muted">(47%)</small></td>
                                                <td> 47.54%</td>
                                                <td>498<small class="text-muted">(81%)</small></td>
                                            </tr><!--end tr-->
                                            <tr>
                                                <td><img src="assets/images/browser_logo/in-explorer.png" alt=""
                                                        height="24" class="mr-2">Internet-Explorer</td>
                                                <td>1836<small class="text-muted">(38%)</small></td>
                                                <td> 41.12%</td>
                                                <td>455<small class="text-muted">(74%)</small></td>
                                            </tr><!--end tr-->
                                            <tr>
                                                <td><img src="assets/images/browser_logo/opera.png" alt=""
                                                        height="24" class="mr-2">Opera</td>
                                                <td>1958<small class="text-muted">(31%)</small></td>
                                                <td> 36.82%</td>
                                                <td>361<small class="text-muted">(61%)</small></td>
                                            </tr><!--end tr-->

                                        </tbody>
                                    </table> <!--end table-->
                                </div><!--end /div-->
                            </div>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col--> --}}
        </div><!--end row-->

        {{-- <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Pages View by Users</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Today<i class="las la-angle-down ml-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Today</a>
                                        <a class="dropdown-item" href="#">Yesterday</a>
                                        <a class="dropdown-item" href="#">Last Week</a>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <ul class="list-group custom-list-group mb-n3">
                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                <div class="media">
                                    <img src="assets/images/small/img-2.jpg" height="40"
                                        class="mr-3 align-self-center rounded" alt="...">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">Dastyle - Admin Dashboard</h6>
                                        <a href="#" class="font-12 text-primary">analytic-index.html</a>
                                    </div><!--end media body-->
                                </div>
                                <div class="align-self-center">
                                    <span class="text-muted mb-n2">4.3k</span>
                                    <div class="apexchart-wrapper w-30 align-self-center">
                                        <div id="dash_spark_1" class="chart-gutters"></div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                <div class="media">
                                    <img src="assets/images/small/img-1.jpg" height="40"
                                        class="mr-3 align-self-center rounded" alt="...">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">Metrica Simple- Admin Dashboard</h6>
                                        <a href="#" class="font-12 text-primary">sales-index.html</a>
                                    </div><!--end media body-->
                                </div>
                                <div class="align-self-center">
                                    <span class="text-muted mb-n2">3.7k</span>
                                    <div class="apexchart-wrapper w-30 align-self-center">
                                        <div id="dash_spark_2" class="chart-gutters"></div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                <div class="media">
                                    <img src="assets/images/small/img-4.jpg" height="40"
                                        class="mr-3 align-self-center rounded" alt="...">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">Crovex - Admin Dashboard</h6>
                                        <a href="#" class="font-12 text-primary">helpdesk-index.html</a>
                                    </div><!--end media body-->
                                </div>
                                <div class="align-self-center">
                                    <span class="text-muted mb-n2">2.9k</span>
                                    <div class="apexchart-wrapper w-30 align-self-center">
                                        <div id="dash_spark_3" class="chart-gutters"></div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                <div class="media">
                                    <img src="assets/images/small/img-5.jpg" height="40"
                                        class="mr-3 align-self-center rounded" alt="...">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">Annex - Admin Dashboard</h6>
                                        <a href="#" class="font-12 text-primary">calendar.html</a>
                                    </div><!--end media body-->
                                </div>
                                <div class="align-self-center">
                                    <span class="text-muted mb-n2">1.2k</span>
                                    <div class="apexchart-wrapper w-30 align-self-center">
                                        <div id="dash_spark_4" class="chart-gutters"></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
           <!--end col-->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Activity</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        All<i class="las la-angle-down ml-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Purchases</a>
                                        <a class="dropdown-item" href="#">Emails</a>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body">
                        <div class="analytic-dash-activity" data-simplebar>
                            <div class="activity">
                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <i class="las la-user-clock bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Donald</span>
                                                updated the status of <a href="">Refund #1234</a> to awaiting
                                                customer response
                                            </p>
                                            <small class="text-muted">10 Min ago</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <i class="mdi mdi-timer-off bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Lucy Peterson</span>
                                                was added to the group, group name is <a href="">Overtake</a>
                                            </p>
                                            <small class="text-muted">50 Min ago</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <img src="assets/images/users/user-5.jpg" alt=""
                                            class="rounded-circle thumb-md">
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Joseph Rust</span>
                                                opened new showcase <a href="">Mannat #112233</a> with theme market
                                            </p>
                                            <small class="text-muted">10 hours ago</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <i class="mdi mdi-clock-outline bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Donald</span>
                                                updated the status of <a href="">Refund #1234</a> to awaiting
                                                customer response
                                            </p>
                                            <small class="text-muted">Yesterday</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <i class="mdi mdi-alert-outline bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Lucy Peterson</span>
                                                was added to the group, group name is <a href="">Overtake</a>
                                            </p>
                                            <small class="text-muted">14 Nov 2019</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <img src="assets/images/users/user-4.jpg" alt=""
                                            class="rounded-circle thumb-md">
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-muted mb-0 font-13 w-75"><span>Joseph Rust</span>
                                                opened new showcase <a href="">Mannat #112233</a> with theme market
                                            </p>
                                            <small class="text-muted">15 Nov 2019</small>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end activity-->
                        </div><!--end analytics-dash-activity-->
                    </div> <!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

        </div><!--end row--> --}}

    </div><!-- container -->
    
@endsection
@section('custom_js')
<script>
     lucide.createIcons();
    document.addEventListener('DOMContentLoaded', function () {
        const months = @json(array_column($monthlyData, 'month'));
        const income = @json(array_column($monthlyData, 'income'));
        const expense = @json(array_column($monthlyData, 'expense'));
        const profit = @json(array_column($monthlyData, 'profit'));

        var options = {
            chart: {
                height: 400,
                type: 'bar',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    }
                }
            },
            series: [
                {
                    name: 'Income',
                    data: income
                },
                {
                    name: 'Expense',
                    data: expense
                },
                {
                    name: 'Profit',
                    data: profit
                }
            ],
            colors: ['#28a745', '#dc3545', '#007bff'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded',
                    borderRadius: 5
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: months,
                labels: {
                    style: {
                        fontSize: '13px',
                        fontWeight: 500,
                        colors: '#6c757d'
                    }
                },
                axisBorder: {
                    color: '#dee2e6'
                },
                axisTicks: {
                    color: '#dee2e6'
                }
            },
            yaxis: {
                title: {
                    text: 'Amount (PKR)',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    formatter: function (val) {
                        return "₨ " + val.toLocaleString();
                    },
                    style: {
                        fontSize: '13px',
                        colors: '#6c757d'
                    }
                }
            },
            grid: {
                borderColor: '#f1f3f5',
                row: {
                    colors: ['#f9f9f9', 'transparent'], // alternate rows
                    opacity: 0.5
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    gradientToColors: undefined,
                    opacityFrom: 0.85,
                    opacityTo: 0.65,
                    stops: [0, 100]
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return "₨ " + val.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '14px',
                fontWeight: 500,
                labels: {
                    colors: '#495057'
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#ana_dash_2"), options);
        chart.render();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const monthlyData = @json($monthlyData);

        function getColorByPercentage(percent) {
            if (percent < 40) return '#FF4560';   // red
            if (percent < 70) return '#FEB019';   // yellow
            return '#00E396';                     // green
        }

        const chartOptions = {
            chart: {
                height: 220,
                type: "radialBar"
            },
            series: [monthlyData[0].profit_percentage],
            colors: [getColorByPercentage(monthlyData[0].profit_percentage)],
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: "70%"
                    },
                    dataLabels: {
                        showOn: "always",
                        name: { show: false },
                        value: {
                            formatter: function (val) {
                                return val + "%";
                            },
                            fontSize: "20px",
                            color: "#333"
                        }
                    }
                }
            },
            labels: ["Profit"]
        };

        const chart = new ApexCharts(document.querySelector("#monthlyProfitChart"), chartOptions);
        chart.render();

        document.getElementById('monthSelector').addEventListener('change', function () {
            const selectedIndex = this.value;
            const selected = monthlyData[selectedIndex];

            chart.updateOptions({
                series: [selected.profit_percentage],
                colors: [getColorByPercentage(selected.profit_percentage)],
            });

            document.getElementById('profitText').textContent = selected.profit_percentage + "% Profit";
        });
    });
</script>
@endsection