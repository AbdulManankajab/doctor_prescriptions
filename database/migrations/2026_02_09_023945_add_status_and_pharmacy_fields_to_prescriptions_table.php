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
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'status')) {
                $table->string('status')->nullable()->default('draft')->after('notes');
            }
            if (!Schema::hasColumn('prescriptions', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('prescriptions', 'dispensed_at')) {
                $table->timestamp('dispensed_at')->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('prescriptions', 'dispensed_by')) {
                $table->foreignId('dispensed_by')->nullable()->after('dispensed_at')->constrained('pharmacy_users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['dispensed_by']);
            $table->dropColumn(['status', 'sent_at', 'dispensed_at', 'dispensed_by']);
        });
    }
};
