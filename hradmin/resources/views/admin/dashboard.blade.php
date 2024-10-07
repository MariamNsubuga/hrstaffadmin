@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin dashboard!</p>
    <a href="{{ route('admin.create') }}" class="btn btn-primary">Add New Admin</a>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Staff Count</div>
                <div class="card-body">
                    <h2>{{ $staffCount }}</h2>
                    <p>Total number of staff members.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Generated Codes</div>
                <div class="card-body">
                    <h2>{{ $codeCount }}</h2>
                    <p>Total number of codes generated.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add a button or link to navigate to the Audit Logs -->
    <div class="mt-4">
        <a href="{{ route('admin.audits') }}" class="btn btn-primary">View Audit Logs</a>
    </div>
</div>
@endsection
