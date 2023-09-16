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
        Schema::create('web_site_lists', function (Blueprint $table) {
          $table->id();
          $table->string('website')->nullable();
          $table->string('website_url')->nullable();
          $table->string('web_site_code')->nullable();
          $table->string('bot_name')->default('Help Desk');
          $table->integer('category_id')->nullable();
          $table->string('massage')->nullable();
          $table->integer('status')->default(1);
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
        Schema::dropIfExists('web_site_lists');
    }
};
