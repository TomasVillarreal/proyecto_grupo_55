<?php

namespace App\Models;

use CodeIgniter\Model;

class MedidaProductoModel extends Model
{
    protected $table = 'medida_producto';//El nombre de la tabla en nuestra bd
    protected $primaryKey = 'id_medida_producto';

    protected $allowedFields = [//La única fila editable
        'nombre_medida'
    ];
}