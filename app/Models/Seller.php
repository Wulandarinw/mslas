<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'ktp_nik',
        'customer_id',
        'status_seller',
        'ktp_nama',
        'ktp_tempat_lahir',
        'ktp_birth',
        'ktp_jk',
        'ktp_gol_darah',
        'ktp_alamat',
        'ktp_rt',
        'ktp_rw',
        'ktp_kel_desa',
        'ktp_kecamatan',
        'ktp_agama',
        'ktp_status_perkawinan',
        'ktp_pekerjaan',
        'ktp_kewarganegaraan',
        'ktp_picture',
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shops()
    {
        return $this->hasOne(Shop::class, 'seller_ktp_nik', 'ktp_nik');
    }
}
