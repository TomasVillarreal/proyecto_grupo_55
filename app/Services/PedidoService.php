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

    public function obtenerPedidoEspecifico(int $id_pedido)
    {
        $pedido = $this->pedidoModel->obtenerPedidoEspecifico($id_pedido);
        if (!$pedido) {
            throw new \Exception("Pedido no encontrado");
        }
        return $pedido;
    }

    // Metodo para el rechazo de un pedido, consiste en cambiar el estado del pedido unicamente.
    public function rechazarPedido(int $idPedido, string $mensajeRechazo): bool
    {
        $pedido = $this->pedidoModel->find($idPedido);

        // el estado == 1 corresponde al estado "Pendiente". si no esta en este estado
        // significa que ya fue aceptado o rechazado
        if (!$pedido || $pedido->id_estado_pedido != 1) {
            throw new \InvalidArgumentException("El producto ya fue rechazado/aceptado.");
        }

        return $this->pedidoModel->update($idPedido, [
            'id_estado_pedido' => 3,
            'motivo_cancelacion_pedido' => $mensajeRechazo
        ]);
    }

    // Metodo para la aprobacion de un pedido, consiste en cambiar el estado del pedido unicamente.
    public function aprobarPedido(int $idPedido): bool
    {
        $pedido = $this->pedidoModel->find($idPedido);

        // el estado == 1 corresponde al estado "Pendiente". si no esta en este estado
        // significa que ya fue aceptado o rechazado
        if (!$pedido || $pedido->id_estado_pedido != 1) {
            throw new \InvalidArgumentException("El producto ya fue rechazado/aceptado.");
        }

        return $this->pedidoModel->update($idPedido, [
            'id_estado_pedido' => 2
        ]);
    }
}