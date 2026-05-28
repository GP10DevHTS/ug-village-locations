<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ug_parishes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            if (config('ug-village-locations.use_uuids', false)) {
                $table->uuid('uuid')->unique()->nullable();
            }
            $table->unsignedBigInteger('sub_county_id')->index();
            $table->string('name')->index();
            $table->timestamps();

            $table->foreign('sub_county_id')->references('id')->on('ug_sub_counties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ug_parishes');
    }
};
