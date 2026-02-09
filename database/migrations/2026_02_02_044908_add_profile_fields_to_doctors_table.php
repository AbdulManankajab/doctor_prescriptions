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
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('qualification')->nullable()->after('specialization');
            $table->integer('experience_years')->nullable()->after('qualification');
            $table->text('address')->nullable()->after('experience_years');
            $table->text('bio')->nullable()->after('address');
            $table->string('profile_picture')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['phone', 'qualification', 'experience_years', 'address', 'bio', 'profile_picture']);
        });
    }
};
