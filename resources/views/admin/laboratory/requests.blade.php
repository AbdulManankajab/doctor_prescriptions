@extends('admin.layouts.admin')

@section('title', 'All Laboratory Requests')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold mb-0 text-teal" style="color: #0d9488;">All Laboratory Requests</h3>
        <p class="text-muted">Overview of all blood tests and medical laboratory requests.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Tests</th>
                        <th>Status</th>
                        <th>Staff</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->created_at->format('d M y, h:i A') }}</td>
                            <td class="fw-bold">{{ $request->patient->name }}</td>
                            <td>Dr. {{ $request->doctor->name }}</td>
                            <td>{{ Str::limit($request->requested_tests, 30) }}</td>
                            <td>
                                @if($request->status === 'Pending')
                                    <span class="badge bg-secondary rounded-pill">Pending</span>
                                @elseif($request->status === 'In Progress')
                                    <span class="badge bg-warning text-dark rounded-pill">In Progress</span>
                                @else
                                    <span class="badge bg-success rounded-pill">Completed</span>
                                @endif
                            </td>
                            <td>{{ $request->completedBy ? $request->completedBy->name : '-' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-3" style="border-color: #0d9488; color: #0d9488;" data-bs-toggle="modal" data-bs-target="#requestModal{{ $request->id }}">
                                    <i class="bi bi-info-circle me-1"></i> Details
                                </button>

                                <!-- Simple Detail Modal -->
                                <div class="modal fade" id="requestModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold modal-title">Lab Results: {{ Str::limit($request->requested_tests, 40) }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="row mb-3">
                                                    <div class="col-md-6 text-muted small fw-bold text-uppercase">Tests Requested</div>
                                                    <div class="col-12 mt-1">{{ $request->requested_tests }}</div>
                                                </div>
                                                @if($request->status === 'Completed')
                                                <hr>
                                                <div class="row mb-3">
                                                    <div class="col-md-6 text-muted small fw-bold text-uppercase">Laboratory Findings</div>
                                                    <div class="col-12 mt-1 bg-light p-3 rounded font-monospace">{!! nl2br(e($request->report)) !!}</div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No laboratory requests found.</td>
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
@endsection
