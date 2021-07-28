<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetsDocTimestampLsitingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rets_doc_timestamp
        if (! Schema::hasColumn('residential_listings', 'rets_doc_timestamp')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('rets_doc_timestamp')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'rets_doc_timestamp')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('rets_doc_timestamp')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'rets_doc_timestamp')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('rets_doc_timestamp')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'rets_doc_timestamp')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('rets_doc_timestamp')->nullable();
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
        // rets_doc_timestamp
        if (! Schema::hasColumn('residential_listings', 'rets_doc_timestamp')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('rets_doc_timestamp');
            });
        }
        if (! Schema::hasColumn('land_listings', 'rets_doc_timestamp')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('rets_doc_timestamp');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'rets_doc_timestamp')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('rets_doc_timestamp');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'rets_doc_timestamp')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('rets_doc_timestamp');
            });
        }
    }
}
