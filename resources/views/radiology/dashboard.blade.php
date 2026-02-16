@extends('layouts.radiology')

@section('title', 'Radiology Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Radiology Requests</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Test</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('d M, h:i A') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $request->patient->name }}</div>
                                        <div class="small text-muted">{{ $request->patient->patient_number }}</div>
                                    </td>
                                    <td>Dr. {{ $request->doctor->name }}</td>
                                    <td>{{ Str::limit($request->test_name, 30) }}</td>
                                    <td>
                                        @if($request->priority === 'Urgent')
                                            <span class="badge bg-danger rounded-pill">Urgent</span>
                                        @else
                                            <span class="badge bg-light text-dark border rounded-pill">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status === 'Pending')
                                            <span class="badge bg-secondary rounded-pill">Pending</span>
                                        @elseif($request->status === 'In Progress')
                                            <span class="badge bg-warning text-dark rounded-pill">In Progress</span>
                                        @else
                                            <span class="badge bg-success rounded-pill">Completed</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('radiology.show', $request->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View & Process
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
