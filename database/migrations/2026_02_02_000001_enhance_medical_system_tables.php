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
        // 1. Patient Allergies Table
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('allergy_name');
            $table->enum('allergy_type', ['medicine', 'food', 'other'])->default('other');
            $table->timestamps();
        });

        // 2. Examinations Table
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->text('notes')->comment('Symptoms, Physical findings, Vital signs, Clinical observations');
            $table->timestamps();
        });

        // 3. Examination Files Table
        Schema::create('examination_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type')->comment('pdf, jpg, png');
            $table->timestamps();
        });

        // 4. Diagnoses Table
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->string('primary_diagnosis');
            $table->string('secondary_diagnosis')->nullable();
            $table->timestamps();
        });

        // 5. Update Prescriptions Table
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('examination_id')->nullable()->after('doctor_id')->constrained()->onDelete('set null');
            $table->foreignId('diagnosis_id')->nullable()->after('examination_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['examination_id']);
            $table->dropForeign(['diagnosis_id']);
            $table->dropColumn(['examination_id', 'diagnosis_id']);
        });

        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('examination_files');
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('patient_allergies');
    }
};
