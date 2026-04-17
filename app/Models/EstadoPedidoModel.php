<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoPedidoModel extends Model
{
    protected $table = 'estado_pedido';//Nuestra tabla en la bd
    protected $primaryKey = 'id_estado_pedido';

    protected $allowedFields = [
        'tipo_estado_pedido'
    ];
}