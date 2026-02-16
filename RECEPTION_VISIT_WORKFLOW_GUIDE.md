# 🏥 Reception Panel & Visit Number Workflow - Complete Implementation Guide

## ✅ System Status: FULLY IMPLEMENTED

Your Hospital Electronic Prescription System now has a complete **Reception Panel and Visit Number Workflow** integrated seamlessly with existing modules.

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Database Schema](#database-schema)
3. [Reception Panel Features](#reception-panel-features)
4. [Visit Workflow](#visit-workflow)
5. [Doctor Integration](#doctor-integration)
6. [Pharmacy & Investigation Integration](#pharmacy--investigation-integration)
7. [Authentication & Security](#authentication--security)
8. [Key Features](#key-features)
9. [Usage Guide](#usage-guide)
10. [API Reference](#api-reference)

---

## 🎯 Overview

### What's Implemented

✅ **Reception Login Panel** - Separate authentication system for reception staff
✅ **Patient Registration** - Search existing or create new patients
✅ **Visit Management** - Create and track patient visits with unique visit numbers
✅ **Doctor Assignment** - Assign visits to specific doctors
✅ **Visit Token Printing** - Generate printable visit tokens
✅ **Doctor Search Integration** - Doctors can search by visit number
✅ **Prescription Linking** - Prescriptions automatically linked to visits
✅ **Investigation Integration** - X-Ray & Lab requests reference visit_id
✅ **Backward Compatibility** - All existing features remain unchanged

---

## 🗄️ Database Schema

### Tables

#### `reception_staff` Table
```sql
CREATE TABLE reception_staff (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    status BOOLEAN DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### `visits` Table
```sql
CREATE TABLE visits (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    visit_number VARCHAR(255) UNIQUE NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    assigned_doctor_id BIGINT UNSIGNED NOT NULL,
    visit_date DATE NOT NULL,
    status ENUM('open', 'completed') DEFAULT 'open',
    reception_user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (reception_user_id) REFERENCES reception_staff(id) ON DELETE CASCADE
);
```

### Foreign Key Additions (Backward Compatible)

All existing tables have **nullable** `visit_id` columns:

- `prescriptions.visit_id` (nullable)
- `radiology_requests.visit_id` (nullable)
- `laboratory_requests.visit_id` (nullable)

---

## 🎫 Reception Panel Features

### 1. Reception Dashboard
**URL:** `/reception/dashboard`

**Features:**
- View all visits (paginated)
- Search by visit number, patient name, or phone
- Create new visits
- Print visit tokens
- Real-time visit status tracking

### 2. Patient Search & Registration
**Endpoint:** `GET /reception/patient/search`

**Workflow:**
```
1. Reception searches by name/phone/ID
2. If patient exists → Load patient data
3. If not → Create new patient
4. System prevents duplicate by phone number
```

### 3. Visit Creation
**Endpoint:** `POST /reception/visit/store`

**Business Rules:**
✅ One patient can only have ONE open visit per day
✅ Visit number auto-generated: `RX` + `YYYYMMDD` + `0001`
✅ Visit number = Prescription number (when linked)
✅ Automatically assigns reception staff

**Validation:**
```php
[
    'patient_id' => 'nullable|exists:patients,id',
    'name' => 'required_without:patient_id|string|max:255',
    'age' => 'required_without:patient_id|integer|min:1',
    'gender' => 'required_without:patient_id|in:Male,Female,Other',
    'phone' => 'required|string|max:20',
    'address' => 'nullable|string',
    'assigned_doctor_id' => 'required|exists:doctors,id',
]
```

### 4. Token Printing
**URL:** `/reception/visit/print/{id}`

**Features:**
- Printable visit token
- Includes patient details
- Shows assigned doctor
- Displays visit number prominently
- QR code support ready

---

## 🩺 Doctor Integration

### 1. Enhanced Patient Search
**Endpoint:** `POST /doctor/search-patient`

**Search Priority:**
1. **Visit Number** (highest priority)
2. Patient Name
3. Patient Phone
4. Patient Number

```javascript
// Example: Doctor searches "RX202602140001"
// System finds visit and returns patient with visit_id attached
{
    "id": 1,
    "name": "John Doe",
    "age": 35,
    "phone": "123456789",
    "visit_id": 5  // ← Attached automatically
}
```

### 2. Prescription Creation

**URL:** `/doctor/prescription/create/{patientId}?visit_id={id}`

**Workflow:**
```
1. Doctor searches patient (by visit number or name)
2. If visit_id present → Load visit details
3. Patient data pre-filled from visit
4. Prescription created with visit_id link
5. Prescription number = Visit number (if linked)
6. On finalization → Visit status changes to 'completed'
```

### 3. Visit Status Management

**Status Flow:**
```
open (Reception creates) 
  ↓
completed (Doctor finalizes prescription)
```

---

## 💊 Pharmacy & Investigation Integration

### Radiology Requests
**Table:** `radiology_requests`
**Fields:**
- `visit_id` (nullable) - Links to visit
- `prescription_id` (nullable) - Links to prescription

### Laboratory Requests
**Table:** `laboratory_requests`
**Fields:**
- `visit_id` (nullable) - Links to visit
- `prescription_id` (nullable) - Links to prescription

### Pharmacy Dispensing
**Workflow:**
```
1. Pharmacy searches by prescription number
2. If linked to visit → Shows visit number
3. Dispense using visit number or prescription number
```

---

## 🔐 Authentication & Security

### Guards Configuration
```php
'guards' => [
    'reception' => [
        'driver' => 'session',
        'provider' => 'reception_staff',
    ],
    'doctor' => [
        'driver' => 'session',
        'provider' => 'doctors',
    ],
    // ... other guards
],
```

### Reception Login
**URL:** `/reception/login`
**Credentials:** Email + Password
**Session:** Managed via `reception` guard

### Admin Management
Reception staff are created and managed by admin users through:
- `AdminReceptionController`
- Admin can create, update, activate/deactivate reception users

---

## 🌟 Key Features

### 1. Duplicate Prevention
```php
// Prevents same patient having multiple open visits on same day
$existingVisit = Visit::where('patient_id', $patientId)
    ->whereDate('visit_date', today())
    ->where('status', 'open')
    ->first();

if ($existingVisit) {
    return back()->with('error', 'Patient already has an open visit for today.');
}
```

### 2. Visit Number Format
**Format:** `RX` + `YYYYMMDD` + `####`

**Examples:**
- `RX202602140001` - First visit on February 14, 2026
- `RX202602140025` - 25th visit on same day

**Auto-Generation:**
```php
static::creating(function ($visit) {
    if (empty($visit->visit_number)) {
        $visit->visit_number = 'RX' . date('Ymd') 
            . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
});
```

### 3. Patient Data Consistency
- Reception can update patient details during visit creation
- Doctor can update patient details during prescription
- Phone number used as primary unique identifier
- Latest data always preferred

### 4. Audit Trail
**Tracked Fields:**
- `reception_user_id` - Who created the visit
- `created_at` - When visit was created
- `updated_at` - Last modification
- `visit_date` - Actual visit date
- `assigned_doctor_id` - Which doctor

---

## 📖 Usage Guide

### For Reception Staff

#### **Step 1: Login**
1. Navigate to `/reception/login`
2. Enter email and password
3. Access dashboard

#### **Step 2: Create Visit**
1. Click "New Visit" button
2. Search patient by name/phone
   - If exists → Select from dropdown
   - If not → Enter new patient details
3. Fill required fields:
   - Name, Age, Gender, Phone
   - Address (optional)
   - Assigned Doctor (required)
4. Click "Create Visit"
5. System generates visit number automatically

#### **Step 3: Print Token**
1. Find visit in dashboard
2. Click "Print Token" button
3. Print and give to patient

### For Doctors

#### **Step 1: Search Patient**
1. Enter visit number in search box (e.g., `RX202602140001`)
2. **OR** Search by patient name/phone
3. System loads patient with visit attached

#### **Step 2: Create Prescription**
1. Patient details pre-filled from visit
2. Add examination notes
3. Request investigations (optional)
4. Add medicines
5. Finalize prescription
   - Prescription number = Visit number
   - Visit status → completed

### For Admin

#### **Manage Reception Staff**
1. Navigate to Admin → Reception Management
2. Create new reception user:
   - Name, Email, Phone
   - Password
   - Status (Active/Inactive)
3. Edit or deactivate as needed

---

## 🔌 API Reference

### Reception Routes
```php
// Authentication
POST   /reception/login
POST   /reception/logout
GET    /reception/login

// Dashboard
GET    /reception/dashboard
GET    /reception/patient/search?query={search}
POST   /reception/visit/store
GET    /reception/visit/print/{id}
```

### Doctor Routes (Visit-Enhanced)
```php
// Patient Search (now includes visit number search)
POST   /doctor/search-patient

// Prescription Creation (visit-aware)
GET    /doctor/prescription/create/{patientId}?visit_id={id}
POST   /doctor/prescription/store
```

### Models & Relationships

#### Visit Model
```php
class Visit extends Model
{
    // Relationships
    public function patient()      // belongsTo Patient
    public function doctor()       // belongsTo Doctor (assigned)
    public function receptionStaff() // belongsTo ReceptionStaff
    public function prescription()   // hasOne Prescription
    public function radiologyRequests() // hasMany
    public function laboratoryRequests() // hasMany
}
```

#### Prescription Model
```php
class Prescription extends Model
{
    // Added relationship
    public function visit() // belongsTo Visit
}
```

---

## ✨ System Flow Diagram

```
┌─────────────────┐
│   Reception     │
│    Creates      │
│     Visit       │
└────────┬────────┘
         │
         │ Generates Visit Number
         │ (RX202602140001)
         ▼
┌─────────────────┐
│  Prints Token   │
│   for Patient   │
└────────┬────────┘
         │
         │ Patient goes to doctor
         ▼
┌─────────────────┐
│  Doctor Searches│
│  by Visit Number│
└────────┬────────┘
         │
         │ Patient data loaded
         ▼
┌─────────────────┐
│ Doctor Creates  │
│  Prescription   │
│  (linked to     │
│   visit_id)     │
└────────┬────────┘
         │
         │ Can request X-Ray/Lab
         │ (also linked to visit_id)
         ▼
┌─────────────────┐
│ Doctor Finalizes│
│  → Visit Status │
│    = completed  │
└────────┬────────┘
         │
         │ Prescription Number
         │ = Visit Number
         ▼
┌─────────────────┐
│  Sent to        │
│  Pharmacy       │
└─────────────────┘
```

---

## 🛡️ Safety & Backward Compatibility

### All Existing Features Preserved
✅ Doctor panel works without visits (legacy mode)
✅ Prescriptions can be created without visit_id
✅ Investigations work independently
✅ Pharmacy dispenses with or without visit reference
✅ All old prescriptions remain functional

### Database Safety
✅ All visit_id columns are **nullable**
✅ No existing columns removed
✅ Foreign keys use `ON DELETE SET NULL`
✅ Migration is reversible

### Code Safety
✅ Visit lookup wrapped in conditional checks
✅ Graceful fallbacks if visit not found
✅ No breaking changes to existing methods
✅ Additional parameters are optional

---

## 📊 Benefits Summary

### For Reception
- ✅ Streamlined patient registration
- ✅ Organized visit tracking
- ✅ Professional token printing
- ✅ Reduced wait times

### For Doctors
- ✅ Pre-filled patient data
- ✅ Quick visit number search
- ✅ Better patient flow management
- ✅ Complete visit history

### For Hospital Management
- ✅ Better audit trails
- ✅ Visit analytics capability
- ✅ Staff performance tracking
- ✅ Improved patient experience

### For Pharmacy
- ✅ Clear visit reference
- ✅ Better prescription tracking
- ✅ Reduced dispensing errors

---

## 🚀 Next Steps (Optional Enhancements)

While the system is fully functional, you could consider:

1. **Visit Analytics Dashboard**
   - Daily visit counts
   - Doctor workload distribution
   - Average visit completion time

2. **SMS Notifications**
   - Token number via SMS
   - Queue status updates

3. **Queue Management System**
   - Display board showing current visit
   - Patient queue position

4. **Report Generation**
   - Reception visit reports
   - Doctor consultation reports
   - Visit turnaround time analysis

5. **Mobile App Integration**
   - Patient check-in via app
   - Digital token on phone

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue:** "Patient already has an open visit for today"
**Solution:** Check existing visit or complete previous visit first

**Issue:** Visit number not showing in doctor search
**Solution:** Ensure visit is assigned to that specific doctor

**Issue:** Prescription number doesn't match visit number
**Solution:** Check if prescription was created via visit workflow

---

## 📝 Conclusion

Your **Reception Panel & Visit Number Workflow** is now fully integrated and production-ready. The system provides:

- ✅ Complete hospital workflow from reception to prescription
- ✅ Seamless integration with existing modules
- ✅ Backward compatibility with legacy data
- ✅ Professional, user-friendly interfaces
- ✅ Robust error handling and validation
- ✅ Comprehensive audit capabilities

All features requested in your requirements have been implemented and are working together harmoniously!

---

**Last Updated:** February 14, 2026  
**Version:** 1.0.0  
**Status:** Production Ready ✅
