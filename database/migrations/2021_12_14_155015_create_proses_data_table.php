<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProsesDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proses_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('id_cpm');
            $table->string('penerus');
            $table->string('izin');
            $table->string('earliest_time');
            $table->string('latest_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proses_data');
    }
}
