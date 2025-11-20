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
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable()->after('user_id');
            $table->foreign('period_id')->references('id')->on('work_periods')->onDelete('set null');
            $table->index('period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropIndex(['period_id']);
            $table->dropColumn('period_id');
        });
    }
};
