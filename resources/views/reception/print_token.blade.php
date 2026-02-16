<!DOCTYPE html>
<html>
<head>
    <title>Visit Token - {{ $visit->visit_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 300px; /* Adjust for thermal printer width */
            margin: 0 auto;
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .token-info {
            margin: 20px 0;
        }
        .visit-number {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .patient-info {
            text-align: left;
            margin: 10px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="header">
        <h3>MEDICAL CENTER</h3>
        <p>Outpatient Department</p>
    </div>

    <div class="token-info">
        <p>Date: {{ $visit->created_at->format('d-M-Y') }}</p>
        <p>Time: {{ $visit->created_at->format('h:i A') }}</p>
        
        <h3>VISIT NUMBER</h3>
        <div class="visit-number">{{ $visit->visit_number }}</div>
    </div>

    <div class="patient-info">
        <p><strong>Patient:</strong> {{ $visit->patient->name }}</p>
        <p><strong>Age/Gender:</strong> {{ $visit->patient->age }} / {{ $visit->patient->gender }}</p>
        <p><strong>Phone:</strong> {{ $visit->patient->phone }}</p>
        <p><strong>Doctor:</strong> Dr. {{ $visit->doctor->name }}</p>
        <p><strong>Specialization:</strong> {{ $visit->doctor->specialization ?? 'General' }}</p>
    </div>

    <div class="footer">
        <p>Please wait for your number to be called.</p>
        <p>Valid for today only.</p>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Print Again</button>
        <button onclick="window.close()">Close</button>
    </div>

</body>
</html>
