<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table = 'pedido';//Nuestra tabla en la bd
    protected $primaryKey = 'id_pedido';

    protected $allowedFields = [
        'fecha_solicitud_pedido',
        'comentario_pedido',
        'motivo_cancelacion_pedido',
        'id_estado_pedido',
        'id_servicio_medico'
    ];
}