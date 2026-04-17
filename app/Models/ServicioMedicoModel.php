<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioMedicoModel extends Model
{
    protected $table = 'servicio_medico';
    protected $primaryKey = 'id_servicio_medico';

    protected $allowedFields = [
        'nombre_servicio_medico'
    ];
}