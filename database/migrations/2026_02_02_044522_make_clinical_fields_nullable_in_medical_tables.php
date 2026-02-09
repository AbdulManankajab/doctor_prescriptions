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
        Schema::table('examinations', function (Blueprint $table) {
            $table->text('notes')->nullable()->change();
        });

        Schema::table('diagnoses', function (Blueprint $table) {
            $table->string('primary_diagnosis')->nullable()->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('diagnosis')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->text('notes')->nullable(false)->change();
        });

        Schema::table('diagnoses', function (Blueprint $table) {
            $table->string('primary_diagnosis')->nullable(false)->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('diagnosis')->nullable(false)->change();
        });
    }
};
