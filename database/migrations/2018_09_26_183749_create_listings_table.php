<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('acreage')->nullable();
            $table->string('area')->nullable()->index();
            $table->decimal('baths')->nullable();
            $table->decimal('baths_full')->nullable();
            $table->decimal('baths_half')->nullable();
            $table->decimal('bedrooms')->nullable();
            $table->decimal('cib_ceiling_height')->nullable();
            $table->decimal('cib_front_footage')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('co_la_code')->nullable();
            $table->string('co_lo_code')->nullable();
            $table->dateTime('date_modified')->nullable();
            $table->text('directions')->nullable();
            $table->text('ftr_constrc')->nullable();
            $table->text('ftr_energy')->nullable();
            $table->text('ftr_exterior')->nullable();
            $table->text('ftr_forklift')->nullable();
            $table->text('ftr_hoaincl')->nullable();
            $table->text('ftr_interior')->nullable();
            $table->text('ftr_lotaccess')->nullable();
            $table->text('ftr_lotdesc')->nullable();
            $table->text('ftr_ownership')->nullable();
            $table->text('ftr_parking')->nullable();
            $table->text('ftr_projfacilities')->nullable();
            $table->text('ftr_sitedesc')->nullable();
            $table->text('ftr_transportation')->nullable();
            $table->text('ftr_utilities')->nullable();
            $table->text('ftr_waterfront')->nullable();
            $table->text('ftr_waterview')->nullable();
            $table->text('ftr_zoning')->nullable();
            $table->string('la_code')->nullable();
            $table->text('legals')->nullable();
            $table->string('legal_block')->nullable();
            $table->string('legal_lot')->nullable();
            $table->string('legal_unit')->nullable();
            $table->date('list_date')->nullable();
            $table->integer('list_price')->nullable()->index();
            $table->string('lot_dimensions')->nullable();
            $table->string('lo_code')->nullable();
            $table->string('mls_acct')->nullable()->index();
            $table->decimal('num_units')->nullable();
            $table->string('occupancy_yn')->nullable();
            $table->string('parcel_id')->nullable();
            $table->decimal('parking_spaces')->nullable();
            $table->string('parking_type')->nullable();
            $table->decimal('photo_count')->nullable();
            $table->dateTime('photo_date_modified')->nullable();
            $table->string('proj_name')->nullable();
            $table->string('prop_type')->nullable();
            $table->string('public_show_address')->nullable();
            $table->text('remarks')->nullable();
            $table->string('res_hoa_fee')->nullable();
            $table->string('res_hoa_term')->nullable();
            $table->string('sa_code')->nullable();
            $table->date('sold_date')->nullable();
            $table->integer('sold_price')->nullable();
            $table->string('so_code')->nullable();
            $table->string('so_name')->nullable();
            $table->decimal('sqft_total')->nullable();
            $table->string('state')->nullable();
            $table->string('status')->nullable();
            $table->string('stories')->nullable();
            $table->string('street_name')->nullable()->index();
            $table->decimal('street_num')->nullable();
            $table->string('subdivision')->nullable()->index();
            $table->string('sub_area')->nullable();
            $table->decimal('tot_heat_sqft')->nullable();
            $table->string('unit_num')->nullable();
            $table->decimal('wf_feet')->nullable();
            $table->decimal('year_built')->nullable();
            $table->string('zip')->nullable()->index();
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
        Schema::dropIfExists('listings');
    }
}
