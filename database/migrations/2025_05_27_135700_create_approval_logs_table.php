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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_id')->nullable()->constrained()->onDelete('cascade');
          
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['approved', 'rejected','resubmission','resubmitted','paid']);
            $table->text('comment')->nullable();
            $table->timestamps();

            // $table->foreign('replenishment_id')->references('id')->on('replenishments')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
