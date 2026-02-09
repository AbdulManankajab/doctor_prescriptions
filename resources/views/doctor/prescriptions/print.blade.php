@extends('layouts.doctor')

@section('title', 'Print Prescription')

@section('styles')
<style>
    :root {
        --primary-color: #2c3e50;
        --accent-color: #34495e;
    }

    @media print {
        @page {
            size: A5;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background: white !important;
        }
        .no-print {
            display: none !important;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .print-pad {
            box-shadow: none !important;
            border: none !important;
        }
    }

    .print-pad {
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #333;
        background: white;
        padding: 20px 30px; /* Reduced top padding */
        width: 148mm;
        min-height: 210mm;
        margin: 0 auto;
        position: relative;
    }

    /* Header Styling */
    .pad-header {
        text-align: center;
        margin-bottom: 15px;
        border-bottom: 1.5px solid var(--primary-color);
        padding-bottom: 15px;
        padding-top: 0; /* Removed top padding */
        position: relative;
    }

    .doctor-info-box {
        position: absolute;
        top: 0;
        right: 0;
        text-align: right;
        width: auto;
    }

    .dr-name {
        font-size: 13px;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 2px;
    }

    .dr-creds {
        font-size: 9px;
        line-height: 1.1;
        color: #555;
    }

    .patient-info-box {
        position: absolute;
        top: 0;
        left: 0;
        text-align: left;
        width: auto;
        font-size: 11px;
    }

    .patient-label {
        font-weight: bold;
        color: var(--primary-color);
        display: inline-block;
        width: 35px;
    }

    .facility-branding {
        margin: 0 auto;
        display: inline-block;
        width: 100%;
        padding-top: 5px;
    }

    .facility-logo {
        max-height: 50px;
        max-width: 140px;
        margin-bottom: 5px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .facility-name {
        font-size: 18px;
        font-weight: bold;
        color: var(--primary-color);
        text-transform: uppercase;
        margin: 0;
    }

    .facility-details {
        font-size: 10px;
        color: #666;
        margin-top: 2px;
        line-height: 1.2;
    }

    /* Patient Info Bar */
    .patient-bar {
        display: flex;
        justify-content: space-between; /* Restored to space-between */
        background: #f8f9fa;
        padding: 6px 10px;
        margin-bottom: 12px;
        border-radius: 4px;
        font-size: 11px;
    }

    .patient-field {
        display: flex;
        gap: 6px;
    }

    .label {
        font-weight: bold;
        color: var(--primary-color);
    }

    /* Body Layout */
    .pad-body {
        display: flex;
        min-height: 400px; /* Reduced for A5 */
    }

    .sidebar {
        width: 25%;
        border-right: 1px solid #ddd;
        padding-right: 15px;
    }

    .main-content {
        width: 75%;
        padding-left: 15px;
    }

    .section-title {
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 8px;
        display: block;
        color: var(--primary-color);
        border-bottom: 1px solid #eee;
        padding-bottom: 3px;
    }

    .rx-symbol {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 10px;
        display: block;
    }

    .medicine-list {
        column-count: 2;
        column-gap: 20px;
        display: block; /* Ensure columns work */
    }

    .medicine-item {
        margin-bottom: 12px;
        padding-left: 5px;
        break-inside: avoid; /* Prevent splitting across columns */
        display: block;
    }

    .med-name {
        font-weight: bold;
        font-size: 11px; /* Slightly smaller for columns */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .med-details {
        font-size: 9px;
        color: #666;
        margin-top: 1px;
        line-height: 1.1;
    }

    /* Footer */
    .pad-footer {
        position: absolute;
        bottom: 30px;
        left: 30px;
        right: 30px;
        border-top: 1.5px solid var(--primary-color);
        padding-top: 10px;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
    }
</style>
@endsection

@section('content')
<div class="row no-print mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary px-4 rounded-pill shadow-sm">
                <i class="bi bi-printer-fill me-2"></i> Print (A5)
            </button>
            
            @if($prescription->status === 'draft')
                <form action="{{ route('doctor.prescriptions.send', $prescription->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success px-4 rounded-pill shadow-sm" onclick="return confirm('Send this prescription to the hospital pharmacy? Once sent, it will be locked from editing.')">
                        <i class="bi bi-send-fill me-2"></i> Send to Pharmacy
                    </button>
                </form>
            @elseif($prescription->status === 'sent')
                <span class="badge bg-warning text-dark p-2 rounded-pill"><i class="bi bi-clock-history me-1"></i> Sent to Pharmacy</span>
            @elseif($prescription->status === 'dispensed')
                <span class="badge bg-success p-2 rounded-pill"><i class="bi bi-check-all me-1"></i> Dispensed by Pharmacy</span>
            @endif
        </div>
        <a href="{{ Auth::guard('pharmacy')->check() ? route('pharmacy.dashboard') : route('doctor.dashboard') }}" class="btn btn-outline-secondary px-4 rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="container p-0">
    <div class="print-pad">
        @php
            $facility = $prescription->facility_snapshot ?? ($prescription->doctor->facility ? $prescription->doctor->facility->toArray() : null);
        @endphp

        <!-- Header -->
        <div class="pad-header">
            <!-- Patient Info (Top-Left) -->
            <div class="patient-info-box">
                <div><span class="patient-label">Name:</span> {{ $prescription->patient->name }}</div>
                <div><span class="patient-label">Age:</span> {{ $prescription->patient->age }}</div>
                <div><span class="patient-label">Date:</span> {{ $prescription->created_at->format('d-m-Y') }}</div>
            </div>

            <!-- Doctor Info (Top-Right) -->
            <div class="doctor-info-box">
                <div class="dr-name">Dr. {{ $prescription->doctor->name }}</div>
                <div class="dr-creds">
                    {{ $prescription->doctor->qualification }}<br>
                    @if($prescription->doctor->phone)
                        {{ $prescription->doctor->phone }}
                    @endif
                </div>
            </div>

            <!-- Clinic Branding (Center) -->
            <div class="facility-branding">
                @if($facility && !empty($facility['logo_path']))
                    <img src="{{ asset('public/storage/' . $facility['logo_path']) }}" class="facility-logo" alt="Logo">
                @endif
                <h1 class="facility-name">{{ $facility['name'] ?? 'Medical Clinic' }}</h1>
                <div class="facility-details">
                    {{ $facility['address'] ?? '' }} | Phone: {{ $facility['phone'] ?? '' }}
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="pad-body">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="mb-4">
                    <span class="section-title">Clinical Record</span>
                    <div class="small">
                        @if($prescription->diagnosis)
                            {{ $prescription->diagnosis }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div>
                    <span class="section-title">Tests Advised</span>
                    <div class="small text-muted mb-4">-</div>
                </div>

                @if($prescription->qr_token)
                <div class="mt-4 text-center">
                    <div class="qr-code-container d-inline-block p-1 border rounded">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($prescription->qr_token) !!}
                    </div>
                    <div style="font-size: 8px; margin-top: 5px; color: #888;">SCAN TO VERIFY</div>
                </div>
                @endif
            </div>

            <!-- Rx Area -->
            <div class="main-content">
                <span class="rx-symbol">Rx</span>
                
                <div class="medicine-list">
                    @forelse($prescription->items as $item)
                        <div class="medicine-item">
                            <div class="med-name">
                                {{ $loop->iteration }}. {{ $item->medicine->name }} 
                                @if($item->type)
                                    ({{ ucfirst($item->type) }})
                                @endif
                            </div>
                            <div class="med-details">
                                {{ $item->dosage }} | {{ $item->duration }} | <em>{{ $item->instructions }}</em>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No RX items.</p>
                    @endforelse
                </div>

                @if($prescription->notes)
                    <div class="mt-4 pt-2">
                        <span class="section-title">Instructions</span>
                        <div class="mt-1 small" style="white-space: pre-line;">{{ $prescription->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="pad-footer">
            <div><strong>#</strong> {{ $prescription->prescription_number }}</div>
            <div class="text-center">Generated via Prescription System</div>
            <div class="text-end">Signature: ________________</div>
        </div>
    </div>
</div>
@endsection
