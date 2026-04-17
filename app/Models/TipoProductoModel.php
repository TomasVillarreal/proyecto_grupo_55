<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoProductoModel extends Model
{
    protected $table = 'tipo_producto'; //El nombre de nuestra tabla en bd
    protected $primaryKey = 'id_tipo_producto';

    protected $allowedFields = [//La columna de la tabla
        'nombre_tipo_producto'
    ];

}

