<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaterfrontColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // WaterFrontYN
        if (! Schema::hasColumn('residential_listings', 'WaterFrontYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('WaterFrontYN')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'WaterFrontYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('WaterFrontYN')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'WaterFrontYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('WaterFrontYN')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'WaterFrontYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('WaterFrontYN')->nullable();
            });
        }

        // WaterViewYN
        if (! Schema::hasColumn('residential_listings', 'WaterViewYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('WaterViewYN')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'WaterViewYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('WaterViewYN')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'WaterViewYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('WaterViewYN')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'WaterViewYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('WaterViewYN')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // WaterFrontYN
        if (! Schema::hasColumn('residential_listings', 'WaterFrontYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('WaterFrontYN');
            });
        }
        if (! Schema::hasColumn('land_listings', 'WaterFrontYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('WaterFrontYN');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'WaterFrontYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('WaterFrontYN');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'WaterFrontYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('WaterFrontYN');
            });
        }

        // WaterViewYN
        if (! Schema::hasColumn('residential_listings', 'WaterViewYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('WaterViewYN');
            });
        }
        if (! Schema::hasColumn('land_listings', 'WaterViewYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('WaterViewYN');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'WaterViewYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('WaterViewYN');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'WaterViewYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('WaterViewYN');
            });
        }
    }
}
