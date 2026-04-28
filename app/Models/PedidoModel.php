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
    protected $useTimestamps = false; //Para no rellenar columnas de tiempo automaticamente.
    protected $returnType = 'object'; //Se especifica el formato de dato a devolver

    public function obtenerPedidos()
    {
        return $this->orderby ('fecha_solicitud_pedido', 'ASC') // Ordenar los pedidos segun su fecha, de forma ascendente
                    ->findAll(); //Trae todos los pedidos que hay en la base de datos
    }
}