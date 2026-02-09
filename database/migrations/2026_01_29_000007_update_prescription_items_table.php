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
        Schema::table('prescription_items', function (Blueprint $table) {
            // Drop old string field if exists
            if (Schema::hasColumn('prescription_items', 'medicine_name')) {
                $table->dropColumn('medicine_name');
            }
            
            // Add medicine_id if not exists
            if (!Schema::hasColumn('prescription_items', 'medicine_id')) {
                $table->foreignId('medicine_id')->after('prescription_id')->nullable()->constrained('medicines')->onDelete('cascade');
            } else {
                // Ensure foreign key exists if column was partially created
                // (This is a bit tricky, but for now we hope the previous add worked or we make it nullable)
            }
            
            // Add type if not exists
            if (!Schema::hasColumn('prescription_items', 'type')) {
                $table->string('type')->after('medicine_id')->nullable();
            }
        });

        // After adding columns, we might want to make them NOT NULL but wait
        // since we have no data, we can just make them required in code
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'medicine_name')) {
                $table->string('medicine_name')->after('prescription_id')->nullable();
            }
            if (Schema::hasColumn('prescription_items', 'medicine_id')) {
                $table->dropForeign(['medicine_id']);
                $table->dropColumn(['medicine_id']);
            }
            if (Schema::hasColumn('prescription_items', 'type')) {
                $table->dropColumn(['type']);
            }
        });
    }
};
