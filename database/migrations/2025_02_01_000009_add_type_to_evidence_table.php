<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 'file_path' is reused to store a raw URL when type is video_link/code_link,
    // so we avoid modifying its NOT NULL/type constraints on any database driver.
    public function up(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->enum('type', ['file', 'video_link', 'code_link'])->default('file');
        });
    }

    public function down(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
