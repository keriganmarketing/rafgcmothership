<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetsDocumentCountToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            // rets_document_count
        if (! Schema::hasColumn('residential_listings', 'rets_document_count')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('rets_document_count')->nullable();
            });
        }
        if (! Schema::hasColumn('land_listings', 'rets_document_count')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->string('rets_document_count')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'rets_document_count')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->string('rets_document_count')->nullable();
            });
        }
        if (! Schema::hasColumn('rental_listings', 'rets_document_count')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('rets_document_count')->nullable();
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
        // rets_document_count
        if (! Schema::hasColumn('residential_listings', 'rets_document_count')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->dropColumn('rets_document_count');
            });
        }
        if (! Schema::hasColumn('land_listings', 'rets_document_count')){
            Schema::table('land_listings', function (Blueprint $table) {
                $table->dropColumn('rets_document_count');
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'rets_document_count')){
            Schema::table('commercial_listings', function (Blueprint $table) {
                $table->dropColumn('rets_document_count');
            });
        }
        if (! Schema::hasColumn('rental_listings', 'rets_document_count')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->dropColumn('rets_document_count');
            });
        }
    }
}
