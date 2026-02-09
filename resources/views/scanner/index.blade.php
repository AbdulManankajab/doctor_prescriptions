@extends('layouts.public')

@section('title', 'Scan Prescription QR Code')

@section('styles')
<style>
    .scanner-card {
        max-width: 600px;
        margin: 0 auto;
    }
    #reader {
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
    }
    #reader__scan_region video {
        border-radius: 10px;
    }
    .scan-status {
        margin-top: 15px;
        padding: 10px;
        border-radius: 8px;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card scanner-card shadow-lg">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0 text-center text-primary">
                <i class="bi bi-qr-code-scan me-2"></i> Scan Prescription QR Code
            </h4>
        </div>
        <div class="card-body">
            <p class="text-center text-muted mb-4">Point your camera at the QR code on the prescription.</p>
            
            <div id="reader"></div>
            
            <div id="scan-status" class="scan-status text-center"></div>

            <div class="mt-4 text-center">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Cancel & Return
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // decodedText should be the qr_token
        console.log(`Code scanned = ${decodedText}`, decodedResult);
        
        // Stop scanning
        html5QrcodeScanner.clear().then(_ => {
            const statusDiv = document.getElementById('scan-status');
            statusDiv.style.display = 'block';
            statusDiv.className = 'scan-status alert alert-info';
            statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> Processing prescription...';

            // Send to server
            fetch('{{ route("scan.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.className = 'scan-status alert alert-success';
                    statusDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i> Redirecting...';
                    window.location.href = data.redirect;
                } else {
                    statusDiv.className = 'scan-status alert alert-danger';
                    statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> ' + data.message;
                    // Restart scanner after 2 seconds
                    setTimeout(() => {
                        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                        statusDiv.style.display = 'none';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.className = 'scan-status alert alert-danger';
                statusDiv.innerHTML = 'An error occurred during scanning.';
            });
        }).catch(error => {
            console.error('Failed to clear scanner', error);
        });
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning
        // console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0
        },
        /* verbose= */ false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
