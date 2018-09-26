<?php

use App\CommercialListing;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommericalListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commercial_listings', function (Blueprint $table) {
            $fields = (new CommercialListing)->getPropertyMetadata();
            $table->increments('id');
            foreach ($fields as $field) {
                if($field['dataType'] == 'boolean') {
                    $table->boolean($field['name'])->default(0)->nullable();
                } elseif ($field['dataType'] == 'integer') {
                    $table->integer($field['name'])->nullable();
                } else {
                    $dataType = $field['dataType'];
                    $table->$dataType($field['name'], $field['length'])->nullable();
                }
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commercial_listings', function (Blueprint $table) {
            $table->dropIfExists('commercial_listings');
        });
    }
}
