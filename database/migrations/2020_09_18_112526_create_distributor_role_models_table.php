<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributorRoleModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_role_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_id')->unsigned();
            $table->string('title');
            $table->string('route');
            $table->string('params')->nullable();
            $table->string('image');
            $table->integer('sequence');
            $table->string('permissions');
            $table->boolean('active')->default(1);
            $table->timestamps();

            $table->foreign('section_id')->references('id')->on('distributor_section_models')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_role_models');
    }
}
