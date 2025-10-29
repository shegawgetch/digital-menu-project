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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id'); // Service provider
    $table->string('customer_name');
    $table->string('phone');
    $table->json('items'); // [{item_id, name, quantity, price}]
    $table->decimal('total_price', 10, 2);
    $table->enum('status', ['pending', 'ready', 'served', 'completed', 'cancelled'])->default('pending');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
