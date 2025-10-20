<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('transactions');
Capsule::schema()->create('transactions', function ($table) {
  $table->id();
  $table->unsignedBigInteger('from_user_id');
  $table->unsignedBigInteger('to_user_id');
  $table->integer('hours');
  $table->string('description');
  $table->timestamps();

  $table->foreign('from_user_id')->references('id')->on('users');
  $table->foreign('to_user_id')->references('id')->on('users');
});
