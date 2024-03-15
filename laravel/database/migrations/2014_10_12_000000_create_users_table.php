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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('loginType')->default(1)->comment('1: Native, 2: Google, 3: Facebook 4: Line');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('thirdPartyId', 255)->nullable()->comment('三方id');
            $table->string('avatar', 255)->nullable()->comment('頭像');
            $table->rememberToken();
            $table->timestamps();
            $table->unique(['loginType', 'email']);
            $table->unique(['loginType', 'thirdPartyId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
