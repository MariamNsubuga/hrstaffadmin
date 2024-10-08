@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Audit Logs</h1>
    
    <div class="mb-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Action</th>
                {{-- <th>Employee Number</th> --}}
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($audits as $audit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $audit->action }}</td>
                    {{-- <td>{{ $audit->employee_number }}</td> --}}
                    <td>{{ $audit->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $audits->links() }} <!-- Pagination links -->
</div>
@endsection
