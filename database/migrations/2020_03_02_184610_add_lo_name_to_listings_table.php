<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoNameToListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'lo_name')){
                Schema::table('listings', function (Blueprint $table) {
                    $table->string('lo_name')->nullable();
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('listings', 'lo_name')){
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('lo_name');
            });
        }
    }
}
