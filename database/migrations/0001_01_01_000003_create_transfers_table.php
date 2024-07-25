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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique(true)->nullable(false);
            $table->string('batch_id')->nullable()->index();
            $table->integer('sender_user_id')->nullable(false)->index();
            $table->foreign('sender_user_id')->references('id')->on('users');
            $table->integer('recipient_user_id')->nullable(false);
            $table->foreign('recipient_user_id')->references('id')->on('users');
            $table->integer('amount')->nullable(false);
            $table->string('description')->nullable();
            $table->enum('status', ['accepted', 'pending', 'error', 'approved', 'declined', 'canceled'])->nullable(false)->default('accepted');
            $table->string('status_message', 510)->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
