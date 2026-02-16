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
use App\Http\Controllers\Admin\AdminRadiologyController;
use App\Http\Controllers\Admin\AdminLaboratoryController;
use App\Http\Controllers\Admin\AdminReceptionController;
use App\Http\Controllers\Pharmacy\PharmacyAuthController;
use App\Http\Controllers\Pharmacy\PharmacyDashboardController;
use App\Http\Controllers\Doctor\DoctorRadiologyController;
use App\Http\Controllers\Doctor\DoctorLaboratoryController;
use App\Http\Controllers\Radiology\RadiologyAuthController;
use App\Http\Controllers\Radiology\RadiologyDashboardController;
use App\Http\Controllers\Laboratory\LaboratoryAuthController;
use App\Http\Controllers\Laboratory\LaboratoryDashboardController;
use App\Http\Controllers\Reception\ReceptionAuthController;
use App\Http\Controllers\Reception\ReceptionDashboardController;
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
        Route::put('/prescription/{id}', [DoctorPrescriptionController::class, 'update'])->name('prescriptions.update');
        Route::post('/prescription/send/{id}', [DoctorPrescriptionController::class, 'sendToPharmacy'])->name('prescriptions.send');
        Route::get('/patient/history/{id}', [DoctorPrescriptionController::class, 'history'])->name('patient.history');

        // Profile Management
        Route::get('/profile', [DoctorProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');

        // Radiology Requests
        Route::get('/radiology/create/{patient}', [DoctorRadiologyController::class, 'create'])->name('radiology.create');
        Route::post('/radiology/store/{patient}', [DoctorRadiologyController::class, 'store'])->name('radiology.store');
        Route::get('/radiology/history/{patient}', [DoctorRadiologyController::class, 'history'])->name('radiology.history');

        // Laboratory Requests
        Route::get('/laboratory/create/{patient}', [DoctorLaboratoryController::class, 'create'])->name('laboratory.create');
        Route::post('/laboratory/store/{patient}', [DoctorLaboratoryController::class, 'store'])->name('laboratory.store');
        Route::get('/laboratory/history/{patient}', [DoctorLaboratoryController::class, 'history'])->name('laboratory.history');
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

        // Radiology Management
        Route::prefix('radiology-manage')->name('radiology.')->group(function () {
            Route::get('/', [AdminRadiologyController::class, 'index'])->name('index');
            Route::get('/create', [AdminRadiologyController::class, 'createStaff'])->name('create');
            Route::post('/store', [AdminRadiologyController::class, 'storeStaff'])->name('store');
            Route::get('/requests', [AdminRadiologyController::class, 'requests'])->name('requests');
        });

        // Laboratory Management
        Route::prefix('laboratory-manage')->name('laboratory.')->group(function () {
            Route::get('/', [AdminLaboratoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminLaboratoryController::class, 'createStaff'])->name('create');
            Route::post('/store', [AdminLaboratoryController::class, 'storeStaff'])->name('store');
            Route::get('/requests', [AdminLaboratoryController::class, 'requests'])->name('requests');
        });
        
        // Reports
        Route::get('/reports', [AdminDashboardController::class, 'index'])->name('reports'); // Simplified for now

        // Receptionists Management
        Route::resource('reception', AdminReceptionController::class);
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

// Radiology Routes
Route::prefix('radiology')->name('radiology.')->group(function () {
    Route::middleware('guest:radiology')->group(function () {
        Route::get('/login', [RadiologyAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [RadiologyAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [RadiologyAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:radiology')->group(function () {
        Route::get('/dashboard', [RadiologyDashboardController::class, 'index'])->name('dashboard');
        Route::get('/request/{radiologyRequest}', [RadiologyDashboardController::class, 'show'])->name('show');
        Route::post('/request/{radiologyRequest}/status', [RadiologyDashboardController::class, 'updateStatus'])->name('update-status');
        Route::post('/request/{radiologyRequest}/complete', [RadiologyDashboardController::class, 'complete'])->name('complete');
    });
});

// Laboratory Routes
Route::prefix('laboratory')->name('laboratory.')->group(function () {
    Route::middleware('guest:laboratory')->group(function () {
        Route::get('/login', [LaboratoryAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LaboratoryAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [LaboratoryAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:laboratory')->group(function () {
        Route::get('/dashboard', [LaboratoryDashboardController::class, 'index'])->name('dashboard');
        Route::get('/request/{laboratoryRequest}', [LaboratoryDashboardController::class, 'show'])->name('show');
        Route::post('/request/{laboratoryRequest}/status', [LaboratoryDashboardController::class, 'updateStatus'])->name('update-status');
        Route::post('/request/{laboratoryRequest}/complete', [LaboratoryDashboardController::class, 'complete'])->name('complete');
    });
});

// Reception Routes
Route::prefix('reception')->name('reception.')->group(function () {
    Route::middleware('guest:reception')->group(function () {
        Route::get('/login', [ReceptionAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ReceptionAuthController::class, 'login'])->name('login.post');
    });
    Route::post('/logout', [ReceptionAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:reception')->group(function () {
        Route::get('/dashboard', [ReceptionDashboardController::class, 'index'])->name('dashboard');
        Route::get('/patient/search', [ReceptionDashboardController::class, 'searchPatient'])->name('patient.search');
        Route::post('/visit/store', [ReceptionDashboardController::class, 'storeVisit'])->name('visit.store');
        Route::get('/visit/print/{id}', [ReceptionDashboardController::class, 'printToken'])->name('visit.print');
    });
});
