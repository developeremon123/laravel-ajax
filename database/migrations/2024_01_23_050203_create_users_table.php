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
            $table->foreignId('role_id')->constrained('roles');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile_no')->unique();
            $table->string('avatar')->nullable();
            $table->foreignId('district_id')->constrained('locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('upazila_id')->constrained('locations')->onUpdate('cascade')->onDelete('cascade');
            $table->string('postal_code');
            $table->text('address');
            $table->string('password');
            $table->enum('status', ['1', '2'])->default('1')->comment("1=active, 2=inactive");
            $table->rememberToken();
            $table->timestamps();
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
