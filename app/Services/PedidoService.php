<?php

namespace App\Services;

use App\Models\PedidoModel;

class PedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected PedidoModel $pedidoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los pedidos existentes, utilizando el método de la bd*/
    public function obtenerPedidos(int $idEstado, int $idServicio, string $orden = 'ASC'): array
    {
        $pedidos = $this->pedidoModel->obtenerPedidos($idEstado, $idServicio, $orden);
        $listadoPedidos = [];//array que contendrá todos los pedidos existentes

        foreach ($pedidos as $pedido) {
            $listadoPedidos[] = [
                'id_pedido' => $pedido->obtenerID(),
                'fecha'     => $pedido->obtenerFechaSolicitud()->format('Y-m-d'),
                'estado'    => $pedido->obtenerEstado()->obtenerNombre(),
                'servicio_medico'  => $pedido->obtenerServicioMedico()->obtenerNombre()
            ];
        }
        return $listadoPedidos;
    }

    // Metodo que obtiene un pedido especifico, usando el metodo del model
    public function obtenerPedidoEspecifico(int $id_pedido) : array
    {
        $pedido = $this->pedidoModel->obtenerPedidoEspecifico($id_pedido);

        if ($pedido === null) {
            throw new \Exception("Pedido no encontrado");
        }
        return [
                'id_pedido'   => $pedido->obtenerID(),
                'fecha'       => $pedido->obtenerFechaSolicitud()->format('Y-m-d'),
                'estado'      => $pedido->obtenerEstado()->obtenerNombre(),
                'servicio'    => $pedido->obtenerServicioMedico()->obtenerNombre(),
                'comentario'  => $pedido->obtenerComentario(),
                'motivo_rechazo' => $pedido->obtenerMotivoRechazo(),
                ];
    }

    // Metodo para el rechazo de un pedido, consiste en cambiar el estado del pedido unicamente.
    public function rechazarPedido(int $idPedido, string $mensajeRechazo): bool
    {
        $pedido = $this->pedidoModel->obtenerPedidoEspecifico($idPedido);

        // el estado == 1 corresponde al estado "Pendiente". si no esta en este estado
        // significa que ya fue aceptado o rechazado
        if ($pedido->obtenerEstado()->obtenerID() !== 1) {
            throw new \InvalidArgumentException("El pedido se encuentra en un estado invalido para su rechazo.");
        }

        return $this->pedidoModel->rechazar($pedido->obtenerID(), $mensajeRechazo);
    }

    // Metodo para la aprobacion de un pedido, consiste en cambiar el estado del pedido unicamente.
    public function aprobarPedido(int $idPedido): bool
    {
        $pedido = $this->pedidoModel->obtenerPedidoEspecifico($idPedido);

        // el estado == 1 corresponde al estado "Pendiente". si no esta en este estado
        // significa que ya fue aceptado o rechazado
        if ($pedido->obtenerEstado()->obtenerID() !== 1) {
            throw new \InvalidArgumentException("El pedido se encuentra en un estado invalido para su aprobación.");
        }

        return $this->pedidoModel->aprobar($pedido->obtenerID());
    }
}