<?php

namespace App\Services;

use App\Models\PedidoModel;

class PedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected $pedidoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->pedidoModel = model(PedidoModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los pedidos existentes, utilizando el método de la bd*/
    public function obtenerPedidos(int $idEstado, int $idServicio): array
    {
        $pedidos = $this->pedidoModel->obtenerPedidos($idEstado, $idServicio);
        $listadoPedidos = [];//array que contendrá todos los pedidos existentes

        foreach ($pedidos as $pedido) {
            $listadoPedidos[] = [
                'id_pedido' => $pedido->id_pedido,
                'fecha'=> $pedido->fecha_solicitud_pedido,
                'estado'=> $pedido->tipo_estado_pedido,
                'servicio_medico' => $pedido->nombre_servicio_medico
            ];
        }
        return $listadoPedidos;
    }
}