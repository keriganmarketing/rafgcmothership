<?php

use App\RentalListing;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentalListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_listings', function (Blueprint $table) {
            $fields = (new RentalListing)->getMetadata();
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
        Schema::dropIfExists('rental_listings');
    }
}
