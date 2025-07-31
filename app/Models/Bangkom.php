<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bangkom extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_baru',
        'nip_lama',
        'glr_depan',
        'glr_belakang',
        'nama',
        'jabatan',
        'jenis_asn',
        'opd',
        'bidang',
        'subbidang',
        'subunor',
        'total_jp',
    ];
}
