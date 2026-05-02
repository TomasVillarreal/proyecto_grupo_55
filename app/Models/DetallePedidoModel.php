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
        'id_proveedor',
        'id_producto'
    ];
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Se especifica el tipo de dato a devolver

    /*Se crea un método para obtener los detalles para el pedido pasado como parametro donde se 
    obtienen con los JOINs necesarios para ver el resto de caracteristicas de otras
    tablas*/

    public function obtenerDetallesPorPedido(int $pedido): array
    {
        $builder = $this->db->table('detalle_pedido dp');//Crea la consulta sobre la tabla especificada
        $builder->select('dp.cantidad_medicamento, pv.nombre_proveedor, 
        p.id_producto, p.dosis_producto, m.nombre_medicamento, tp.nombre_tipo_producto, mp.nombre_medida');
        $builder->join('proveedor pv', 'pv.id_proveedor = dp.id_proveedor');//Se hace el JOIN con la tabla proveedor
        $builder->join('producto_farmaceutico p', 'p.id_producto = dp.id_producto');//Se hace el JOIN con la tabla producto farmaceutico
        $builder->join('medicamento m', 'm.id_medicamento = p.id_medicamento');//Se hace el JOIN con la tabla medicamento, para los datos del producto
        $builder->join('tipo_producto tp', 'tp.id_tipo_producto = p.id_tipo_producto');//Se hace el JOIN con la tabla tipo producto, para los datos del producto
        $builder->join('medida_producto mp', 'mp.id_medida_producto = p.id_medida_producto');//Se hace el JOIN con la tabla medida producto, para los datos del producto


        $builder->where('dp.id_pedido', $pedido);//Donde coincida el id de medicamento

        /*Por una cuestión estética se busca que se devuelvan por nombre del medicamento
        ordenado alfabéticamente y por la dosis ordenada de menor a mayor*/

        $builder->orderBy('m.nombre_medicamento', 'ASC');
        $builder->orderBy('p.dosis_producto', 'ASC');

        //Se obtienen los resultados
        return $builder->get()->getResult();
    }
}