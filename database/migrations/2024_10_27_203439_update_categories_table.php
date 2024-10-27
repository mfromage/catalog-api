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
        Schema::table('categories', function (Blueprint $table) {
            $table->timestampTz('created_at')->change();
            $table->timestampTz('updated_at')->change();
            $table->timestampTz('deleted_at')->change();
            $table->softDeletesTz()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->timestamp('created_at')->change();
            $table->timestamp('updated_at')->change();
            $table->timestamp('deleted_at')->change();
            $table->softDeletes()->change();
        });
    }
};
