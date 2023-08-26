<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jppd extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'jppd';

    protected $fillable = [
        'id_skpd',
        'lokasi',
        'tahun',
        'jumlah_pegawai',
        'total_jp',
        'rata_rata_jp',
    ];
}
