<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function process(Request $request)
    {
        $token = $request->input('token');
        
        $prescription = Prescription::where('qr_token', $token)->first();

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found or invalid QR code.'
            ], 404);
        }

        // Logic for redirection based on role
        $redirectUrl = '';
        
        if (Auth::guard('doctor')->check()) {
            $redirectUrl = route('doctor.prescriptions.show', $prescription->id);
        } elseif (Auth::guard('pharmacy')->check()) {
            $redirectUrl = route('pharmacy.prescriptions.show', $prescription->id);
        } elseif (Auth::guard('admin')->check()) {
            $redirectUrl = route('admin.prescriptions.show', $prescription->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    }
}
