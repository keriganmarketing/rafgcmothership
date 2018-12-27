<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingAgentToResidentialAndCommercialListings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('residential_listings', 'PendingAgent')){
            Schema::table('residential_listings', function (Blueprint $table) {
                $table->string('PendingAgent')->nullable();
            });
        }
        if (! Schema::hasColumn('commercial_listings', 'PendingAgent')){
            Schema::table('commercial_listings', function (Blueprint $table) {
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
        Schema::table('residential_listings', function (Blueprint $table) {
            $table->dropColumn('PendingAgent');
        });
        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->dropColumn('PendingAgent');
        });
    }
}
