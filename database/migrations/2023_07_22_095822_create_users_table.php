<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
          $table->id();
          $table->string('user_id',50)->unique();
          $table->string('name',100);
          $table->string('email',100)->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->boolean('role')->default(0);
          $table->boolean('agent')->default(0);
          $table->dateTime('date_of_joining');
          $table->boolean('status')->default(0);
          $table->boolean('trash')->default(0);
          $table->string('role_manager',50)->default(0);
          $table->rememberToken();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
