<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHomesteadExemptionYNToNavicaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // HomesteadExemptionYN
        if (! Schema::hasColumn('residential_listings', 'HomesteadExemptionYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('HomesteadExemptionYN')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'HomesteadExemptionYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('HomesteadExemptionYN')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'HomesteadExemptionYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('HomesteadExemptionYN')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'HomesteadExemptionYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('HomesteadExemptionYN')->nullable();
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
        // HomesteadExemptionYN
        if (! Schema::hasColumn('residential_listings', 'HomesteadExemptionYN')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('HomesteadExemptionYN');
            });
        }
        if (! Schema::hasColumn('land_listings', 'HomesteadExemptionYN')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('HomesteadExemptionYN');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'HomesteadExemptionYN')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('HomesteadExemptionYN');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'HomesteadExemptionYN')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('HomesteadExemptionYN');
            });
        }
    }
}
