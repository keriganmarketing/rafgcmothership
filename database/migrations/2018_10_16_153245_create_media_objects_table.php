<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_objects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id')->index();
            $table->text('media_remarks')->nullable();
            $table->string('media_type')->nullable();
            $table->integer('media_order')->nullable();
            $table->string('mls_acct')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_preferred')->default(0)->index();
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
        Schema::dropIfExists('media_objects');
    }
}
