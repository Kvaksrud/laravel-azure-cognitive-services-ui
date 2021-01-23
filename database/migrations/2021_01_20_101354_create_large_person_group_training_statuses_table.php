<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLargePersonGroupTrainingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('large_person_group_training_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('large_person_group_id');
            $table->string('status');
            $table->dateTimeTz('createdDateTime');
            $table->dateTimeTz('lastActionDateTime')->nullable();
            $table->dateTimeTz('lastSuccessfulTrainingDateTime')->nullable();
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
        Schema::dropIfExists('large_person_group_training_statuses');
    }
}
