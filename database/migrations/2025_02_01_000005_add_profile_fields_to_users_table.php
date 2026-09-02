<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Additive only. HOD is represented as role=instructor + is_hod=true, so we
    // never need to alter the existing "role" enum/check-constraint column.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_hod')->default(false);
            $table->string('level')->nullable(); // e.g. "Level 200" (students)
            $table->string('gender')->nullable();
            $table->string('student_number')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['is_hod', 'level', 'gender', 'student_number']);
        });
    }
};
