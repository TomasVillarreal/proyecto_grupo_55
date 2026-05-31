<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ProductoFarmaceutico;
use App\Entities\DetallePedido;
use App\Entities\Proveedor;
use App\Entities\Pedido;
use App\Entities\Medicamento;
use App\Entities\TipoProducto;
use App\Entities\MedidaProducto;
use App\Entities\EstadoPedido;
use App\Entities\ServicioMedico;
use DateTime;

class DetallePedidoModel extends Model
{
    protected $table = 'detalle_pedido';//Nuestra tabla en la bd
    protected $primaryKey = 'id_detalle_pedido';

    protected $allowedFields = [
        'cantidad_medicamento',
        'id_pedido',
        'id_proveedor',
        'id_producto'
    ];
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    
    /* Funcion que crea un objeto de la entidad DetallePedido, haciendo uso del objeto Pedido pasado como parametro 
    para la creacion de un pedido, que necesita la creacion de multiples objetos distintos */
    private function crearObjeto(array $r): DetallePedido
    {
        $estado = new EstadoPedido(
        (int) $r['id_estado_pedido'],
        $r['tipo_estado_pedido']
        );
        $servicio = new ServicioMedico(
            (int) $r['id_servicio_medico'],
            $r['nombre_servicio_medico']
        );
        $pedido = new Pedido(
            (int) $r['id_pedido'],
            new DateTime($r['fecha_solicitud_pedido']),
            $r['comentario_pedido'],
            $r['motivo_cancelacion_pedido'],
            $estado,
            $servicio
        );

        $proveedor = new Proveedor(
            (int) $r['id_proveedor'],
            $r['nombre_proveedor']
        );

        $medicamento = new Medicamento(
            (int) $r['id_medicamento'],
            $r['nombre_medicamento'],
            true
        );

        $tipo = new TipoProducto(
            (int) $r['id_tipo_producto'],
            $r['nombre_tipo_producto']
        );

        $medida = new MedidaProducto(
            (int) $r['id_medida_producto'],
            $r['nombre_medida']
        );

        $producto = new ProductoFarmaceutico(
            (int) $r['id_producto'],
            $r['descripcion_producto'] ?? '',
            (float) $r['dosis_producto'],
            $medicamento,
            $tipo,
            $medida,
            true
        );

        return new DetallePedido(
            (int) $r['id_detalle_pedido'],
            (int) $r['cantidad_medicamento'],
            $pedido,
            $proveedor,
            $producto
        );
    }

    /*Se crea un método para obtener los detalles para el pedido pasado como parametro donde se 
    obtienen con los JOINs necesarios para ver el resto de caracteristicas de otras
    tablas y los ids necesarios para realizar la creacion de los objetos DetallePedido*/
    public function obtenerDetallesPorPedido(int $idPedido): array
    {
        $builder = $this->db->table('detalle_pedido dp');
        $builder->select('
            dp.id_detalle_pedido,
            dp.cantidad_medicamento,
            pv.id_proveedor,
            pv.nombre_proveedor,
            p.id_producto,
            p.dosis_producto,
            p.descripcion_producto,
            m.id_medicamento,
            m.nombre_medicamento,
            tp.id_tipo_producto,
            tp.nombre_tipo_producto,
            mp.id_medida_producto,
            mp.nombre_medida,
            pe.id_pedido,
            pe.fecha_solicitud_pedido,
            pe.comentario_pedido,
            pe.motivo_cancelacion_pedido,
            ep.id_estado_pedido,
            ep.tipo_estado_pedido,
            sm.id_servicio_medico,
            sm.nombre_servicio_medico
        ');
        $builder->join('proveedor pv',              'pv.id_proveedor = dp.id_proveedor');
        $builder->join('producto_farmaceutico p',   'p.id_producto = dp.id_producto');
        $builder->join('medicamento m',             'm.id_medicamento = p.id_medicamento');
        $builder->join('tipo_producto tp',          'tp.id_tipo_producto = p.id_tipo_producto');
        $builder->join('medida_producto mp',        'mp.id_medida_producto = p.id_medida_producto');
        $builder->join('Pedido pe',                 'pe.id_pedido = dp.id_pedido');
        $builder->join('Estado_pedido ep',          'ep.id_estado_pedido = pe.id_estado_pedido');
        $builder->join('Servicio_medico sm',        'sm.id_servicio_medico = pe.id_servicio_medico');

        $builder->where('dp.id_pedido', $idPedido);
        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('p.dosis_producto', 'ASC');

        /*Por una cuestión estética se busca que se devuelvan por nombre del medicamento
        ordenado alfabéticamente y por la dosis ordenada de menor a mayor*/

        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('p.dosis_producto', 'ASC');

        //Se obtienen los resultados
        $result = $builder->get()->getResultArray();

        return array_map(fn($r) => $this->crearObjeto($r), $result);
    }
}