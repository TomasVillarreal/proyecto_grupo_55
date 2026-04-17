<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicamentoModel extends Model
{
    protected $table = 'medicamento'; //La tabla de nuestra bd creada en phpmyadmin
    protected $primaryKey = 'id_medicamento';

    protected $allowedFields = [ //Las columnas de la tabla
        'nombre_medicamento',
        'activo_medicamento'
    ];
}