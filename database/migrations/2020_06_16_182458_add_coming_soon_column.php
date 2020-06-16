<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComingSoonColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // sys_Coming_Soon
        if (! Schema::hasColumn('residential_listings', 'sys_Coming_Soon')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('sys_Coming_Soon')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'sys_Coming_Soon')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('sys_Coming_Soon')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'sys_Coming_Soon')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('sys_Coming_Soon')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'sys_Coming_Soon')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('sys_Coming_Soon')->nullable();
            });
        }

        // Coming_Soon_End
        if (! Schema::hasColumn('residential_listings', 'Coming_Soon_End')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('Coming_Soon_End')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'Coming_Soon_End')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('Coming_Soon_End')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'Coming_Soon_End')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('Coming_Soon_End')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'Coming_Soon_End')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('Coming_Soon_End')->nullable();
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
        // sys_Coming_Soon
        if (! Schema::hasColumn('residential_listings', 'sys_Coming_Soon')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('sys_Coming_Soon');
            });
        }
        if (! Schema::hasColumn('land_listings', 'sys_Coming_Soon')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('sys_Coming_Soon');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'sys_Coming_Soon')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('sys_Coming_Soon');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'sys_Coming_Soon')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('sys_Coming_Soon');
            });
        }

        // Coming_Soon_End
        if (! Schema::hasColumn('residential_listings', 'Coming_Soon_End')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('Coming_Soon_End');
            });
        }
        if (! Schema::hasColumn('land_listings', 'Coming_Soon_End')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('Coming_Soon_End');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'Coming_Soon_End')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('Coming_Soon_End');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'Coming_Soon_End')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('Coming_Soon_End');
            });
        }
    }
}
