<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRemarksDataLenthForAllListingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->longText('remarks', 2000)->change();
        });
        Schema::table('residential_listings', function (Blueprint $table) {
            $table->longText('Remarks', 2000)->change();
        });
        Schema::table('land_listings', function (Blueprint $table) {
            $table->longText('Remarks', 2000)->change();
        });
        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->longText('Remarks', 2000)->change();
        });
        Schema::table('rental_listings', function (Blueprint $table) {
            $table->longText('Remarks', 2000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->longText('remarks', 1000)->change();
        });
        Schema::table('residential_listings', function (Blueprint $table) {
            $table->string('Remarks', 850)->change();
        });
        Schema::table('land_listings', function (Blueprint $table) {
            $table->string('Remarks', 850)->change();
        });
        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->string('Remarks', 850)->change();
        });
        Schema::table('rental_listings', function (Blueprint $table) {
            $table->string('Remarks', 850)->change();
        });
    }
}
