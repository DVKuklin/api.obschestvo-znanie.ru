<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('url',50)->nullable();
            $table->unsignedBigInteger('sort')->nullable();
            $table->unsignedBigInteger('section');
            $table->foreign('section')
                  ->references('id')
                  ->on('sections')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
}
