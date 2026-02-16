<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Radiology Staff
        Schema::create('radiology_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Laboratory Staff
        Schema::create('laboratory_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Radiology Requests
        Schema::create('radiology_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->string('test_name');
            $table->text('clinical_notes')->nullable();
            $table->enum('priority', ['Normal', 'Urgent'])->default('Normal');
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
            $table->text('report')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('radiology_staff')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Laboratory Requests
        Schema::create('laboratory_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->text('requested_tests');
            $table->text('clinical_notes')->nullable();
            $table->enum('priority', ['Normal', 'Urgent'])->default('Normal');
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
            $table->text('report')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('laboratory_staff')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Request Files (Polymorphic)
        Schema::create('request_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('request_type'); // RadiologyRequest or LaboratoryRequest
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('uploaded_by_id');
            $table->string('uploaded_by_type'); // Doctor, RadiologyStaff, or LaboratoryStaff
            $table->timestamps();
            
            $table->index(['request_id', 'request_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_files');
        Schema::dropIfExists('laboratory_requests');
        Schema::dropIfExists('radiology_requests');
        Schema::dropIfExists('laboratory_staff');
        Schema::dropIfExists('radiology_staff');
    }
};
