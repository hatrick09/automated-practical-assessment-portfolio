<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            // Default 'approved' so pre-existing rows (created before this migration) stay
            // visible on portfolios; the controller explicitly sets 'pending' for new ones
            // so the HOD approval workflow is real going forward.
            $table->enum('status', ['pending', 'approved'])->default('approved');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('semester_id');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['status', 'reviewed_at']);
        });
    }
};
