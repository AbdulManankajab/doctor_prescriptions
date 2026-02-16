<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Reception Staff Table
        Schema::create('reception_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Create Visits Table
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_number')->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('assigned_doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('visit_date');
            $table->enum('status', ['open', 'completed'])->default('open');
            $table->foreignId('reception_user_id')->constrained('reception_staff')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Add visit_id to existing clinical tables
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('doctor_id')->constrained('visits')->onDelete('set null');
        });

        Schema::table('radiology_requests', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('doctor_id')->constrained('visits')->onDelete('set null');
        });

        Schema::table('laboratory_requests', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('doctor_id')->constrained('visits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_requests', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropColumn('visit_id');
        });

        Schema::table('radiology_requests', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropColumn('visit_id');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropColumn('visit_id');
        });

        Schema::dropIfExists('visits');
        Schema::dropIfExists('reception_staff');
    }
};
