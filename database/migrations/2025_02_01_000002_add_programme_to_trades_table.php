<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Additive only: existing trades keep working with programme_id = null until an admin assigns one.
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->foreignId('programme_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('programme_id');
        });
    }
};
