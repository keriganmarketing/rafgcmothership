<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\OpenHouse;

class CreateOpenHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_houses', function (Blueprint $table) {
            $fields = (new OpenHouse)->getMetadata();
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
        Schema::dropIfExists('open_houses');
    }
}
