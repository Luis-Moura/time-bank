<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('skills');
Capsule::schema()->create('skills', function ($table) {
  $table->id();
  $table->unsignedBigInteger('user_id');
  $table->string('name');
  $table->string('skill_level')->nullable();
  $table->timestamps();

  $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});