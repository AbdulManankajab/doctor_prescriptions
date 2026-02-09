<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Clinic', 'Hospital', 'Polyclinic']);
            $table->string('logo_path')->nullable();
            $table->text('address');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('license_number')->nullable();
            $table->string('province');
            $table->string('district');
            $table->boolean('status')->default(true)->comment('1: active, 0: inactive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
