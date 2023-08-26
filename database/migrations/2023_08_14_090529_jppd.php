<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jppd', function (Blueprint $table) {
            $table->string('id_skpd');
            $table->string('lokasi');
            $table->integer('tahun');
            $table->unsignedInteger('jumlah_pegawai');
            $table->unsignedInteger('total_jp');
            $table->unsignedInteger('rata_rata_jp');
            $table->timestamps();
            $table->primary(['id_skpd', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jppd');
    }
};
