<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLatLngDatatypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('latitude', 11,8)->change();
            $table->decimal('longitude', 11,8)->change();
        });

        Schema::table('residential_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 11,8)->change();
            $table->decimal('Longitude', 11,8)->change();
        });

        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 11,8)->change();
            $table->decimal('Longitude', 11,8)->change();
        });

        Schema::table('land_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 11,8)->change();
            $table->decimal('Longitude', 11,8)->change();
        });

        Schema::table('rental_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 11,8)->change();
            $table->decimal('Longitude', 11,8)->change();
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
            $table->decimal('latitude', 6,2)->change();
            $table->decimal('longitude', 6,2)->change();
        });

        Schema::table('residential_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 12,2)->change();
            $table->decimal('Longitude', 12,2)->change();
        });

        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 12,2)->change();
            $table->decimal('Longitude', 12,2)->change();
        });

        Schema::table('land_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 12,2)->change();
            $table->decimal('Longitude', 12,2)->change();
        });

        Schema::table('rental_listings', function (Blueprint $table) {
            $table->decimal('Latitude', 12,2)->change();
            $table->decimal('Longitude', 12,2)->change();
        });
    }
}
