<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRVsAllowedYNToListingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // RVsAllowedYN
        if (! Schema::hasColumn('residential_listings', 'RVsAllowedYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('RVsAllowedYN')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'RVsAllowedYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('RVsAllowedYN')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'RVsAllowedYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('RVsAllowedYN')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'RVsAllowedYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('RVsAllowedYN')->nullable();
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
        // RVsAllowedYN
        if (! Schema::hasColumn('residential_listings', 'RVsAllowedYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('RVsAllowedYN');
            });
        }
        if (! Schema::hasColumn('land_listings', 'RVsAllowedYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('RVsAllowedYN');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'RVsAllowedYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('RVsAllowedYN');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'RVsAllowedYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('RVsAllowedYN');
            });
        }
    }
}
