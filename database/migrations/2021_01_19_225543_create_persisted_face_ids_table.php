<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersistedFaceIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persisted_face_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('large_person_group_person_id');
            $table->string('persistedFaceId');
            $table->boolean('trained')->default(false);
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
        Schema::dropIfExists('persisted_face_ids');
    }
}
