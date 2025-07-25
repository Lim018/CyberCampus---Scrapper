<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kalender_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('class_type', ['odd', 'even']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalender_akademiks');
    }
};
