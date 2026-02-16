# 🚀 Quick Start Guide - Reception Panel

## ⚡ Get Started in 5 Minutes!

Your Reception Panel and Visit Workflow is **already installed and ready to use**. Follow these simple steps to start using it today.

---

## 📋 Prerequisites Checklist

Before you begin, ensure:
- [x] Laravel application is running
- [x] Database migrations have been run
- [x] At least one doctor exists in the system
- [x] At least one reception staff user has been created by admin

---

## 👤 Step 1: Create Reception User (Admin Task)

### Option A: Via Admin Panel (Recommended)
1. Login to Admin Panel: `/admin/login`
2. Navigate to **Reception Management**
3. Click **"Add New Reception User"**
4. Fill in details:
   - Name: `Sarah Johnson`
   - Email: `reception@hospital.com`
   - Phone: `+93 700 123 456`
   - Password: `your-secure-password`
   - Status: `Active`
5. Click **Save**

### Option B: Via Database (Quick Test)
```sql
INSERT INTO reception_staff (name, email, password, phone, status, created_at, updated_at)
VALUES (
    'Sarah Johnson',
    'reception@hospital.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 'password'
    '+93 700 123 456',
    1,
    NOW(),
    NOW()
);
```

---

## 🏥 Step 2: Login as Reception

1. Navigate to: **`http://your-domain.com/reception/login`**
2. Enter credentials:
   - Email: `reception@hospital.com`
   - Password: `password` (or what you set)
3. Click **Login**
4. You should see the Reception Dashboard! 🎉

---

## 📝 Step 3: Create Your First Visit

### Scenario: New Patient Arrival

```
Patient: Ahmad Khan
Age: 35 years
Gender: Male
Phone: +93 700 555 123
Address: Kabul, District 10
Assigned Doctor: Dr. Akmal
```

### Steps:
1. In Reception Dashboard, click **"New Visit"** button
2. In the search box, type the phone: `+93 700 555 123`
   - Since patient is new, no results found
3. Fill in the form:
   ```
   Name: Ahmad Khan
   Age: 35
   Gender: Male
   Phone: +93 700 555 123
   Address: Kabul, District 10
   Assigned Doctor: Select "Dr. Akmal" from dropdown
   ```
4. Click **"Create Visit"**
5. Success! Visit number generated: `RX202602140001`
6. Click **"Print Token"** button to print for patient

### What Happens:
- ✅ Patient record created (or existing one updated)
- ✅ Visit record created with unique number
- ✅ Visit assigned to selected doctor
- ✅ Reception user logged as creator
- ✅ Status set to "open"
- ✅ Token ready for printing

---

## 🩺 Step 4: Doctor Uses the Visit

### Doctor's Workflow:

1. **Doctor Login**: `/doctor/login`
2. **Search for Patient**:
   - Enter visit number: `RX202602140001`
   - **OR** Enter patient name: `Ahmad Khan`
   - **OR** Enter phone: `+93 700 555 123`
3. **Select Patient** from results
4. **Create Prescription**:
   - Patient details are pre-filled from visit!
   - Prescription number will be same as visit number
5. **Add Examination Notes** (optional)
6. **Request Investigations** (optional):
   - X-Ray
   - Laboratory tests
7. **Add Medicines**
8. **Finalize Prescription**
9. **Automatic Actions**:
   - Prescription number becomes `RX202602140001`
   - Visit status changes to `completed`
   - Prescription linked to visit via `visit_id`

---

## 🔄 Step 5: Handle Returning Patient

### Scenario: Same patient comes next day

```
Patient: Ahmad Khan (already exists)
Phone: +93 700 555 123
```

### Steps:
1. Click **"New Visit"**
2. Search by phone: `+93 700 555 123`
3. **Patient found!** → Details auto-populate
4. You can update any details if needed
5. Select assigned doctor
6. Click **"Create Visit"**
7. New visit number: `RX202602150001` (next day)

### What Happens:
- ✅ System finds existing patient by phone
- ✅ Loads patient details automatically
- ✅ Creates NEW visit for today
- ✅ Prevents duplicate if patient already has open visit today

---

## 🚫 Duplicate Prevention Example

### Scenario: Patient tries to register twice same day

1. **First Registration** (9:00 AM):
   - Visit created: `RX202602140001`
   - Status: `open`

2. **Second Registration** (9:30 AM - same day):
   - Reception tries to create another visit
   - **System blocks it!**
   - Error message: "Patient already has an open visit for today."

### Solution:
- Find existing visit in dashboard
- Use existing visit number
- **OR** Complete first visit before creating new one

---

## 🖨️ Step 6: Print Visit Token

### What's on the Token:
```
┌─────────────────────────────────┐
│    HOSPITAL NAME                │
│                                 │
│    VISIT TOKEN                  │
│    ═══════════════════          │
│                                 │
│    Visit Number:                │
│    RX202602140001               │
│                                 │
│    Patient: Ahmad Khan          │
│    Age: 35 | Gender: Male       │
│    Phone: +93 700 555 123       │
│                                 │
│    Assigned Doctor: Dr. Akmal   │
│    Date: 14 Feb 2026, 9:00 AM   │
│                                 │
│    [QR Code Placeholder]        │
│                                 │
└─────────────────────────────────┘
```

### To Print:
1. Find visit in dashboard
2. Click **"Print"** or **"Token"** button
3. Browser print dialog opens
4. Print or Save as PDF
5. Give to patient

---

## 🔍 Step 7: Search & Track Visits

### Search Options in Dashboard:
- **By Visit Number**: `RX202602140001`
- **By Patient Name**: `Ahmad Khan`
- **By Patient Phone**: `+93 700 555 123`

### View Options:
- All visits (default)
- Filter by doctor
- Filter by date
- Filter by status (open/completed)

---

## 📊 Common Workflows

### Morning Routine
```
1. Login to reception panel
2. Review today's visits
3. As patients arrive:
   - Search existing patient
   - Create visit
   - Assign to available doctor
   - Print token
   - Send patient to waiting area
```

### During Day
```
1. Monitor visit status
2. Create new visits as patients arrive
3. Update patient information if needed
4. Check which doctor has fewer patients
5. Balance workload by assigning smartly
```

### End of Day
```
1. Review all visits for the day
2. Check which are still "open"
3. Follow up with doctors if needed
4. Review statistics for reporting
```

---

## ⚙️ System Configuration

### Visit Number Format
Current format: `RX` + `YYYYMMDD` + `####`

To change prefix, edit: `app/Models/Visit.php`
```php
// Line 65
$visit->visit_number = 'VST' . date('Ymd') . ...
//                      ^^^^
//                      Change prefix here
```

### Daily Counter Reset
Counter automatically resets each day:
- Feb 14: RX202602140001, RX202602140002, ...
- Feb 15: RX202602150001, RX202602150002, ... (starts from 1)

### Time Zone
Set in: `.env`
```env
APP_TIMEZONE=Asia/Kabul
```

---

## 🐛 Troubleshooting

### Problem: Can't login to reception
**Solution:**
1. Verify reception user exists in database
2. Check email and password are correct
3. Ensure status = 1 (active)
4. Clear browser cache and try again

### Problem: "Patient already has open visit"
**Solution:**
1. Check dashboard for existing visit today
2. Use existing visit number
3. **OR** Have doctor complete the existing visit first
4. Then create new visit

### Problem: Doctor dropdown is empty
**Solution:**
1. Login to admin panel
2. Go to Doctors management
3. Ensure at least one doctor exists
4. Check doctor status = 1 (active)

### Problem: Visit number not showing
**Solution:**
1. Check `visits` table in database
2. Verify `visit_number` column has value
3. Check browser console for JavaScript errors
4. Refresh page

---

## 📞 Quick Reference

### Important URLs
```
Reception Login:    /reception/login
Reception Dashboard: /reception/dashboard
Doctor Login:       /doctor/login
Admin Login:        /admin/login
```

### Key Shortcuts
```
Search Patient:  Type in search box, wait 0.5s
New Visit:       Click "+ New Visit" button
Print Token:     Click "Print" icon in visit row
View Visit:      Click anywhere on visit row
```

---

## ✅ Success Indicators

You know it's working when:
- ✅ Reception can login successfully
- ✅ Visit numbers are generated automatically
- ✅ Doctors can search by visit number
- ✅ Prescriptions show correct visit number
- ✅ Duplicate visits are prevented
- ✅ Tokens print correctly

---

## 🎓 Training Tips

### For Reception Staff
1. Practice creating 5-10 test visits
2. Try both new and existing patients
3. Learn to handle duplicate scenarios
4. Practice printing tokens
5. Understand visit status flow

### For Doctors
1. Practice searching by visit number
2. Understand pre-filled data
3. Know how to request investigations
4. See how visit status changes

---

## 📈 Next Steps

Once comfortable with basics:
1. Review full documentation (RECEPTION_VISIT_WORKFLOW_GUIDE.md)
2. Explore advanced features
3. Set up reporting workflows
4. Consider optional enhancements
5. Train all staff members

---

## 🎯 Daily Checklist

### Reception Opening (Morning)
- [ ] Login to reception panel
- [ ] Check system is responsive
- [ ] Review doctor availability
- [ ] Prepare for patient registration

### During Operations
- [ ] Create visits for arriving patients
- [ ] Print tokens promptly
- [ ] Keep dashboard updated
- [ ] Monitor visit progress

### Reception Closing (Evening)
- [ ] Review all visits created today
- [ ] Check for open visits
- [ ] Note any issues for follow-up
- [ ] Logout securely

---

## 💡 Pro Tips

### Tip 1: Phone Number is Key
Always use phone number to search - it's the most reliable unique identifier.

### Tip 2: Update Details
If patient provides updated info (new address, age), update it during visit creation.

### Tip 3: Doctor Workload
Before assigning, check which doctor has fewer patients for better load balancing.

### Tip 4: Print Immediately
Print token right after creating visit - don't delay.

### Tip 5: Keep Search Open
Keep the patient search box visible for quick lookups.

---

## 🎉 You're Ready!

You now know how to:
- ✅ Create reception users
- ✅ Login to reception panel
- ✅ Register new patients
- ✅ Create visits
- ✅ Handle returning patients
- ✅ Print tokens
- ✅ Search and track visits
- ✅ Troubleshoot common issues

**Go ahead and start using the system!**

For detailed technical information, refer to:
- RECEPTION_VISIT_WORKFLOW_GUIDE.md
- VISUAL_WORKFLOW_GUIDE.md
- EXECUTIVE_SUMMARY.md

---

**Need Help?** Check the comprehensive documentation files included with your system.

**Last Updated:** February 14, 2026  
**Version:** 1.0.0 - Quick Start Guide
