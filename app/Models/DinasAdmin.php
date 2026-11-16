<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DinasAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_dinas',
        'name',
        'email',
        'whatsapp',
        'penanggung_jawab',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}

