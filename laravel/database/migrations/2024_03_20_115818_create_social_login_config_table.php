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
        Schema::create('social_login_config', function (Blueprint $table) {
            $table->id();
            $table->integer('loginType')->unique()->comment('1: Native, 2: Google, 3: Facebook 4: Line');
            $table->json('parameters');
            $table->json('apiUrl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_login_config');
    }
};
