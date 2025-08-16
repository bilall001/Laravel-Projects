@extends('admin.layouts.main')
@section('title')
Projects - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">Project Details</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr><th style="width: 200px;">Title</th><td>{{ $project->title }}</td></tr>
                    <tr><th>Type</th><td>{{ ucfirst($project->type) }}</td></tr>
                    <tr><th>Team</th><td>{{ $project->team->name ?? '-' }}</td></tr>
                    <tr><th>Developer</th><td>{{ $project->user->name ?? '-' }}</td></tr>
                    <tr><th>Total Price</th><td>${{ number_format($project->price, 2) }}</td></tr>
                    <tr><th>Paid Price</th><td>${{ number_format($project->paid_price, 2) }}</td></tr>
                    <tr><th>Remaining Price</th><td>${{ number_format($project->remaining_price, 2) }}</td></tr>
                    <tr><th>Duration</th><td>{{ $project->duration }}</td></tr>
                    <tr><th>Start Date</th><td>{{ $project->start_date }}</td></tr>
                    <tr><th>End Date</th><td>{{ $project->end_date }}</td></tr>
                    <tr><th>Developer End Date</th><td>{{ $project->developer_end_date }}</td></tr>
                    <tr>
    <th>Client</th>
    <td>{{ $project->client->name ?? '-' }}</td>
</tr>
                    <tr>
                        <th>File</th>
                        <td>
                            @if($project->file)
                                <a href="{{ asset('storage/' . $project->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
    </div>
</div>
@endsection
