<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVirtualTour4ToListingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('residential_listings', 'Virtual_Tour4')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('Virtual_Tour4')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'Virtual_Tour4')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('Virtual_Tour4')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'Virtual_Tour4')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('Virtual_Tour4')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'Virtual_Tour4')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('Virtual_Tour4')->nullable();
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
        if (! Schema::hasColumn('residential_listings', 'Virtual_Tour4')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('Virtual_Tour4');
            });
        }
        if (! Schema::hasColumn('land_listings', 'Virtual_Tour4')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('Virtual_Tour4');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'Virtual_Tour4')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('Virtual_Tour4');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'Virtual_Tour4')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('Virtual_Tour4');
            });
        }
    }
}
