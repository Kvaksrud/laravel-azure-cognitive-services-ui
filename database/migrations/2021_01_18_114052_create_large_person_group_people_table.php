<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLargePersonGroupPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('large_person_group_people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('personId');
            $table->unsignedBigInteger('large_person_group_id');
            $table->string('name')->nullable();
            $table->text('userData')->nullable();
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
        Schema::dropIfExists('large_person_group_people');
    }
}
