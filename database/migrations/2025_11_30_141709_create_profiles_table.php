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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('locale', 10)->default('en');
            $table->unsignedInteger('current_level')->default(1);
            $table->unsignedInteger('current_xp')->default(0);
            $table->unsignedBigInteger('total_xp')->default(0);
            $table->unsignedBigInteger('coins')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
