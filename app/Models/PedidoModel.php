<?php

namespace App\Models;

use DateTime;
use CodeIgniter\Model;
use App\Entities\Pedido;
use App\Entities\EstadoPedido;
use App\Entities\ServicioMedico;

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
    
    // Funcion que crea un objeto de la entidad Pedido.
    private function crearObjeto(array $r): Pedido
    {
        $estado = new EstadoPedido(
            (int) $r['id_estado_pedido'],
            $r['tipo_estado_pedido']
        );

        $servicio = new ServicioMedico(
            (int) $r['id_servicio_medico'],
            $r['nombre_servicio_medico']
        );

        return new Pedido(
            (int) $r['id_pedido'],
            new DateTime($r['fecha_solicitud_pedido']),
            $r['comentario_pedido'] ?? null,
            $r['motivo_cancelacion_pedido'] ?? null,
            $estado,
            $servicio
        );
    }

    /* Funcion que obtiene todos los registros de la BD que son de la clase pedido.
    También se obtienen con los JOINs necesarios para ver el resto de caracteristicas de otras
    tablas y realizar la creacion de todos los objetos pedidos.*/
    public function obtenerPedidos(int $id_estado, int $id_servicio, string $orden) : array
    {
        $builder = $this->db->table('Pedido p');//Crea la consulta sobre la tabla especificada
        $builder->select(
            'p.id_pedido,
            DATE(p.fecha_solicitud_pedido) as fecha_solicitud_pedido,
            p.comentario_pedido,
            p.motivo_cancelacion_pedido,
            ep.id_estado_pedido,
            ep.tipo_estado_pedido,
            sm.id_servicio_medico,
            sm.nombre_servicio_medico'
        );
        $builder->join('Estado_pedido ep', 'ep.id_estado_pedido = p.id_estado_pedido');//Se hace el JOIN con la tabla Estado_pedido
        $builder->join('Servicio_medico sm', 'sm.id_servicio_medico = p.id_servicio_medico');//Se hace el JOIN con la tabla Servicio_medico
        /* $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');//Se hace el JOIN con la tabla Usuario*/

        if ($id_estado != 0) {
            $builder->where('p.id_estado_pedido', $id_estado);
        }
        if ($id_servicio != 0) {
            $builder->where('p.id_servicio_medico', $id_servicio);
        }

        // Ordeno los pedidos segun el valor del argumento
        $builder->orderBy('p.fecha_solicitud_pedido', $orden);

        //Se obtienen los resultados
        $result = $builder->get()->getResultArray();
        return array_map(fn($r) => $this->crearObjeto($r), $result);
    }

    /* Funcion que devuelve un unico pedido (o ninguno), tal que el pedido devuelto sera aquel
    cuyo id sea igual al pasado como parametro. Trae con joins todos los datos
    necesarios para su uso en la vista, asi como todos los ids necesarios para la creacion del pedido*/
    public function obtenerPedidoEspecifico(int $id_pedido) : ?Pedido
    {
        $builder = $this->db->table('Pedido p');//Crea la consulta sobre la tabla especificada
        $builder->select(
            'p.id_pedido,
            DATE(p.fecha_solicitud_pedido) as fecha_solicitud_pedido,
            p.comentario_pedido,
            p.motivo_cancelacion_pedido,
            ep.id_estado_pedido,
            ep.tipo_estado_pedido,
            sm.id_servicio_medico,
            sm.nombre_servicio_medico'
        );
        $builder->join('Estado_pedido ep', 'ep.id_estado_pedido = p.id_estado_pedido');//Se hace el JOIN con la tabla Estado_pedido
        $builder->join('Servicio_medico sm', 'sm.id_servicio_medico = p.id_servicio_medico');//Se hace el JOIN con la tabla Servicio_medico
        /* $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');//Se hace el JOIN con la tabla Usuario*/

        $builder->where('p.id_pedido', $id_pedido);

        //Se obtienen los resultados
        $result = $builder->get()->getRowArray();
        if (!$result) {
            return null;
        }
        return $this->crearObjeto($result);
    }

    /* Funcion que cambia el id_estado_pedido del objeto cuyo id sea igual al 
    pasado como parametro a 2 (el id = 2 es el id del estado "Aprobado")*/
    public function aprobar (int $idPedido) : bool {
        return $this->update($idPedido, [
            'id_estado_pedido' => 2
        ]);
    }

    /* Funcion que cambia el id_estado_pedido del objeto cuyo id sea igual al 
    pasado como parametro a 3 (el id = 3 es el id del estado "Rechazado"), y le actualiza
    el motivo_cancelacion_pedido del pedido con el motivo pasado como parametro*/
    public function rechazar (int $idPedido, string $mensaje) : bool {
        return $this->update($idPedido, [
            'id_estado_pedido' => 3,
            'motivo_cancelacion_pedido' => $mensaje
        ]);
    }
}