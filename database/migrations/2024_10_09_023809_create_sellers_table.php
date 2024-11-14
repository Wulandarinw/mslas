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
        Schema::create('sellers', function (Blueprint $table) {
            $table->string('ktp_nik')->length(16)->primary();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->enum('status_seller', ['Verified', 'Pending', 'Declined'])->default('Pending');
            $table->string('ktp_nama');
            $table->string('ktp_tempat_lahir');
            $table->date('ktp_birth');
            $table->enum('ktp_jk', ['Laki-laki', 'Perempuan']);
            $table->string('ktp_gol_darah')->length(2);
            $table->string('ktp_alamat');
            $table->string('ktp_rt');
            $table->string('ktp_rw');
            $table->string('ktp_kel_desa');
            $table->string('ktp_kecamatan');
            $table->enum('ktp_agama',['Islam', 'Protestan', 'Katolik', 'Buddha', 'Hindu', 'Khonghucu']);
            $table->enum('ktp_status_perkawinan', ['Belum', 'Kawin', 'Cerai']);
            $table->string('ktp_pekerjaan');
            $table->enum('ktp_kewarganegaraan', ['WNI', 'WNA']);
            $table->string('ktp_picture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
