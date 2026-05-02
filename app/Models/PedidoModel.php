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
        /*'id_usuario'*/
    ];
    protected $useTimestamps = false; //Para no rellenar columnas de tiempo automaticamente.
    protected $returnType = 'object'; //Se especifica el formato de dato a devolver

    public function obtenerPedidos(int $id_estado, int $id_servicio)
    {
        $builder = $this->db->table('Pedido p');//Crea la consulta sobre la tabla especificada
        $builder->select(
            'p.id_pedido,
            DATE(p.fecha_solicitud_pedido) as fecha_solicitud_pedido,
            p.comentario_pedido,
            motivo_cancelacion_pedido,
            ep.tipo_estado_pedido, 
            sm.nombre_servicio_medico');
        $builder->join('Estado_pedido ep', 'ep.id_estado_pedido = p.id_estado_pedido');//Se hace el JOIN con la tabla Estado_pedido
        $builder->join('Servicio_medico sm', 'sm.id_servicio_medico = p.id_servicio_medico');//Se hace el JOIN con la tabla Servicio_medico
        /* $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');//Se hace el JOIN con la tabla Usuario*/

        if ($id_estado != 0) {
            $builder->where('p.id_estado_pedido', $id_estado);
        }
        if ($id_servicio != 0) {
            $builder->where('p.id_servicio_medico', $id_servicio);
        }

        // Se devuelve los pedidos de mas nuevos a mas viejos, en principio.
        $builder->orderBy('p.fecha_solicitud_pedido', 'ASC');

        //Se obtienen los resultados
        return $builder->get()->getResult();
    }

    public function obtenerPedidoEspecifico(int $id_pedido)
    {
        $builder = $this->db->table('Pedido p');//Crea la consulta sobre la tabla especificada
        $builder->select(
            'p.id_pedido,
            DATE(p.fecha_solicitud_pedido) as fecha_solicitud_pedido,
            p.comentario_pedido,
            motivo_cancelacion_pedido,
            ep.tipo_estado_pedido, 
            sm.nombre_servicio_medico');
        $builder->join('Estado_pedido ep', 'ep.id_estado_pedido = p.id_estado_pedido');//Se hace el JOIN con la tabla Estado_pedido
        $builder->join('Servicio_medico sm', 'sm.id_servicio_medico = p.id_servicio_medico');//Se hace el JOIN con la tabla Servicio_medico
        /* $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');//Se hace el JOIN con la tabla Usuario*/

        $builder->where('p.id_pedido', $id_pedido);

        //Se obtienen los resultados
        return $builder->get()->getResult();
    }
}