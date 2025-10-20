<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
