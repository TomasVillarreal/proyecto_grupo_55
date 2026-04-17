<?php

namespace App\Models;

use CodeIgniter\Model;

class DetallePedidoModel extends Model
{
    protected $table = 'detalle_pedido';//Nuestra tabla en la bd
    protected $primaryKey = 'id_detalle_pedido';

    protected $allowedFields = [
        'cantidad_medicamento',
        'id_pedido',
        'id_proveeodr',
        'id_producto'
    ];
}