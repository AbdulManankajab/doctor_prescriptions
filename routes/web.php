<?php

use App\Http\Controllers\Doctor\DoctorAuthController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorPrescriptionController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminPrescriptionController;
use App\Http\Controllers\Admin\AdminMedicineController;
use App\Http\Controllers\Admin\AdminPrescriptionDefaultsController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminPharmacyController;
use App\Http\Controllers\Pharmacy\PharmacyAuthController;
use App\Http\Controllers\Pharmacy\PharmacyDashboardController;
use App\Http\Controllers\PrescriptionScannerController;
use Illuminate\Support\Facades\Route;

// ... (other imports)

// Show landing page on root
Route::get('/', function() {
    return view('landing');
})->name('home');

// QR Scanning Routes
Route::middleware(['auth:doctor,pharmacy,admin'])->group(function () {
    Route::get('/scan-prescription', [PrescriptionScannerController::class, 'index'])->name('scan.index');
    Route::post('/scan-prescription/process', [PrescriptionScannerController::class, 'process'])->name('scan.process');
});

// Fallback login route for Laravel internal redirects
Route::get('/login', function() {
    return redirect()->route('doctor.login');
})->name('login');

// Doctor Authentication Routes
Route::prefix('doctor')->name('doctor.')->group(function () {
    Route::middleware('guest:doctor')->group(function () {
        Route::get('/login', [DoctorAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [DoctorAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [DoctorAuthController::class, 'logout'])->name('logout');

    // Protected Doctor Routes
    Route::middleware('auth:doctor')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        
        // Prescription Management
        Route::post('/search-patient', [DoctorPrescriptionController::class, 'searchPatient'])->name('patient.search');
        Route::get('/prescription/create/{patientId?}', [DoctorPrescriptionController::class, 'create'])->name('prescription.create');
        Route::post('/prescription/store', [DoctorPrescriptionController::class, 'store'])->name('prescription.store');
        Route::get('/prescription/print/{id}', [DoctorPrescriptionController::class, 'print'])->name('prescriptions.print');
        Route::get('/prescription/{id}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::post('/prescription/send/{id}', [DoctorPrescriptionController::class, 'sendToPharmacy'])->name('prescriptions.send');
        Route::get('/patient/history/{id}', [DoctorPrescriptionController::class, 'history'])->name('patient.history');

        // Profile Management
        Route::get('/profile', [DoctorProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/export-anonymized', [AdminDashboardController::class, 'exportAnonymized'])->name('export.anonymized');
        
        // Patients Management
        Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{id}', [AdminPatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{id}/edit', [AdminPatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [AdminPatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');
        
        // Prescriptions Management
        Route::get('/prescriptions', [AdminPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{id}', [AdminPrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/prescriptions/{id}/print', [AdminPrescriptionController::class, 'print'])->name('prescriptions.print');
        Route::delete('/prescriptions/{id}', [AdminPrescriptionController::class, 'destroy'])->name('prescriptions.destroy');

        // Medicines Management
        Route::resource('medicines', AdminMedicineController::class);

        // Default Notes Management
        Route::resource('defaults', AdminPrescriptionDefaultsController::class);

        // Doctors Management
        Route::resource('doctors', AdminDoctorController::class);

        // Facilities Management
        Route::resource('facilities', AdminFacilityController::class);

        // Pharmacy Management
        Route::resource('pharmacy', AdminPharmacyController::class);
        
        // Reports
        Route::get('/reports', [AdminDashboardController::class, 'index'])->name('reports'); // Simplified for now
    });
});

// Pharmacy Routes
Route::prefix('pharmacy')->name('pharmacy.')->group(function () {
    Route::middleware('guest:pharmacy')->group(function () {
        Route::get('/login', [PharmacyAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [PharmacyAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [PharmacyAuthController::class, 'logout'])->name('logout');

    // Protected Pharmacy Routes
    Route::middleware('auth:pharmacy')->group(function () {
        Route::get('/dashboard', [PharmacyDashboardController::class, 'index'])->name('dashboard');
        Route::get('/prescription/{id}', [PharmacyDashboardController::class, 'show'])->name('prescriptions.show');
        Route::post('/prescription/{id}/dispense', [PharmacyDashboardController::class, 'dispense'])->name('prescriptions.dispense');
        Route::get('/prescription/{id}/print', [PharmacyDashboardController::class, 'print'])->name('prescriptions.print');
    });
});
