<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->text('qr_code_path')->nullable(); // to store QR code image path or SVG
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('qr_code_path');
    });
}

};
