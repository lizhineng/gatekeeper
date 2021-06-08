<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatekeeperTables extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('assigned_permissions', function (Blueprint $table) {
            $table->id();
            $table->morphs('assignable');
            $table->unsignedBigInteger('permission_id')->index();
            $table->timestamps();
        });

        Schema::create('assigned_roles', function (Blueprint $table) {
            $table->id();
            $table->morphs('assignable');
            $table->unsignedBigInteger('role_id')->index();
            $table->timestamps();
        });
    }
}