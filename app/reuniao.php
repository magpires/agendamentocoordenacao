<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reuniao extends Model
{
    protected $fillable = [
        'titulo',
        'id_coordenador',
        'id_solicitante',
        'id_secretario',
        'status',
        'local',
        'start',
        'end',
    ];
}
