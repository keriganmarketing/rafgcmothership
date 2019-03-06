<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonthlyRentToListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('listings', 'monthly_rent')){
            Schema::table('listings', function (Blueprint $table) {
                $table->string('monthly_rent')->nullable();
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
        if (! Schema::hasColumn('listings', 'monthly_rent')){
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('monthly_rent');
            });
        }
    }
}
