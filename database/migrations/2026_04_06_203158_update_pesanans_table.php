<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Ubah tipe kolom sesuai ERD
            $table->integer('total')->change();                        // dari string ke integer
            $table->integer('metode_bayar')->nullable()->change();     // dari string ke integer
            $table->smallInteger('status_bayar')->default(0)->change(); // dari string ke smallInteger

            // Hapus kolom yang tidak ada di ERD
            $table->dropColumn('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Kembalikan ke tipe semula
            $table->string('total')->change();
            $table->string('metode_bayar')->nullable()->change();
            $table->string('status_bayar')->default('unpaid')->change();

            // Tambahkan kembali kolom yang dihapus
            $table->string('snap_token')->nullable();
        });
    }
};
