<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingAgentToRentalDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('rental_listings', 'PendingAgent')){
            Schema::table('rental_listings', function (Blueprint $table) {
                $table->string('PendingAgent')->nullable();
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
        Schema::table('rental_listings', function (Blueprint $table) {
            $table->dropColumn('PendingAgent');
        });
    }
}
