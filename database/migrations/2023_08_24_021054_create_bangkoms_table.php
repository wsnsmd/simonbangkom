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
        Schema::create('bangkoms', function (Blueprint $table) {
            $table->string('nip_baru');
            $table->string('nip_lama');
            $table->string('glr_depan')->nullable();
            $table->string('glr_belakang')->nullable();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('opd');
            $table->unsignedInteger('total_jp');
            $table->integer('tahun');
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
        Schema::dropIfExists('bangkoms');
    }
};
