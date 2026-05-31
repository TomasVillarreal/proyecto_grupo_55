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
    private function crearObjeto(array $r, Pedido $pedido): DetallePedido
    {
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
    public function obtenerDetallesPorPedido(Pedido $pedido): array
    {
        $builder = $this->db->table('detalle_pedido dp');//Crea la consulta sobre la tabla especificada
        $builder->select('
            dp.id_detalle_pedido,
            dp.cantidad_medicamento,
            pv.id_proveedor,
            pv.nombre_proveedor,
            p.id_producto,
            p.dosis_producto,
            m.id_medicamento,
            m.nombre_medicamento,
            tp.id_tipo_producto,
            tp.nombre_tipo_producto,
            mp.id_medida_producto,
            mp.nombre_medida
        ');
        $builder->join('proveedor pv', 'pv.id_proveedor = dp.id_proveedor');//Se hace el JOIN con la tabla proveedor
        $builder->join('producto_farmaceutico p', 'p.id_producto = dp.id_producto');//Se hace el JOIN con la tabla producto farmaceutico
        $builder->join('medicamento m', 'm.id_medicamento = p.id_medicamento');//Se hace el JOIN con la tabla medicamento, para los datos del producto
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = p.id_tipo_producto');//Se hace el JOIN con la tabla tipo producto, para los datos del producto
        $builder->join('medida_producto mp', 'mp.id_medida_producto = p.id_medida_producto');//Se hace el JOIN con la tabla medida producto, para los datos del producto


        $builder->where('dp.id_pedido', $pedido->obtenerID());//Donde coincida el id de medicamento

        /*Por una cuestión estética se busca que se devuelvan por nombre del medicamento
        ordenado alfabéticamente y por la dosis ordenada de menor a mayor*/

        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('p.dosis_producto', 'ASC');

        //Se obtienen los resultados
        $result = $builder->get()->getResultArray();

        return array_map(fn($r) => $this->crearObjeto($r, $pedido), $result);
    }
}