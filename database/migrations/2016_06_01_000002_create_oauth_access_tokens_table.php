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
        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Corrected line to use UUID
            $table->uuid('user_id')->index(); // Change this line to use UUID
            $table->foreign('user_id')->references('_id')->on('users'); // Assuming _id is the UUID column in the users table
            $table->uuid('client_id');
            $table->string('name')->nullable();
            $table->json('scopes');
            $table->boolean('revoked');
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_access_tokens');
    }
};