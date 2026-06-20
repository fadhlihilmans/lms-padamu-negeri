<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiGrade extends Model
{
    protected $table = 'konfigurasi_grade';

    protected $fillable = [
        'nilai_min',
        'nilai_max',
        'grade',
        'predikat',
    ];
}
