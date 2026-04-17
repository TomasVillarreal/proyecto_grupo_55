<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoFarmaceuticoModel extends Model
{
    protected $table = 'producto_farmaceutico'; //Nuestra tabla en la bd
    protected $primaryKey = 'id_producto';

    protected $allowedFields = [//Las columnas de la tabla
        'descripcion_producto',
        'dosis_producto',
        'activo_producto',
        'id_medicamento',
        'id_tipo_producto',
        'id_medida_producto'
    ];
}