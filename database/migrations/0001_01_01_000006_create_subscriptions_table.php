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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable(false);
            $table->integer('price')->nullable(false);
            $table->string('frequency')->nullable(false);
            $table->string('status')->nullable(false);
            $table->string('status_message')->nullable();
            $table->date('start_date')->nullable(false);
            $table->date('next_invoice_date')->nullable(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
