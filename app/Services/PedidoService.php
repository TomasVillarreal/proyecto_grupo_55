<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;

class PedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected PedidoModel $pedidoModel;
    protected DetallePedidoModel $detallePedidoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();//Se reconoce e instancia la clase
        $this->detallePedidoModel = new DetallePedidoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los pedidos existentes, utilizando el método de la bd*/
    public function obtenerListadoPedidos(int $idEstado, int $idServicio, string $orden = 'ASC'): array
    {
        $pedidos = $this->pedidoModel->obtenerPedidos($idEstado, $idServicio, $orden);
        $listadoPedidos = [];//array que contendrá todos los pedidos existentes

        foreach ($pedidos as $pedido) {
            $listadoPedidos[] = [
                'id_pedido' => $pedido->obtenerID(),
                'fecha'     => $pedido->obtenerFechaSolicitud()->format('Y-m-d'),
                'estado'    => $pedido->obtenerEstado()->obtenerNombre(),
                'servicio_medico'  => $pedido->obtenerServicioMedico()->obtenerNombre(),
                'usuario' => $pedido->obtenerUsuario()->obtenerNombreCompleto(),
                'rol' => $pedido->obtenerUsuario()->obtenerRol()->obtenerNombre()
            ];
        }
        return $listadoPedidos;
    }

    // Metodo que obtiene un pedido especifico, usando el metodo del model
    public function obtenerPedidoEspecifico(int $id_pedido) : array
    {
        $pedido = $this->pedidoModel->obtenerPorID($id_pedido);

        if ($pedido === null) {
            throw new \Exception("Pedido no encontrado");
        }
        
        return [
                'id_pedido'   => $pedido->obtenerID(),
                'id_usuario' => $pedido->obtenerUsuario()->obtenerID(),
                'fecha'       => $pedido->obtenerFechaSolicitud()->format('Y-m-d'),
                'estado'      => $pedido->obtenerEstado()->obtenerNombre(),
                'servicio'    => $pedido->obtenerServicioMedico()->obtenerNombre(),
                'comentario'  => $pedido->obtenerComentario(),
                'motivo_rechazo' => $pedido->obtenerMotivoRechazo(),
                'usuario' => $pedido->obtenerUsuario()->obtenerNombreCompleto(),
                'rol' => $pedido->obtenerUsuario()->obtenerRol()->obtenerNombre(),
                ];
                
    }

    // Metodo para el rechazo de un pedido, consiste en cambiar el estado del pedido unicamente.
    public function rechazarPedido(int $idPedido, string $mensajeRechazo): bool
    {
        $pedido = $this->pedidoModel->obtenerPorID($idPedido);

        /*Antes de rechazar el pedido, tomamos el id del usuario en sesion, para luego
        manejar el hecho de que un mismo usuario no pueda rechazar su propio pedido*/
        $idUsuarioSesion = session()->get('id_usuario');
        
        //Se verifica que el usuario en sesion no sea el mismo que creo el pedido
        if ($idUsuarioSesion === $pedido->obtenerUsuario()->obtenerID()) {

            throw new \InvalidArgumentException(
                'No puede rechazar un pedido creado por usted mismo.'
            );
        }        

        // Verificacion que se hace si no existe el objeto
        if ($pedido === null) {
            throw new \InvalidArgumentException('Pedido no encontrado.');
        }

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
        //Obtenemos el pedido
        $pedido = $this->pedidoModel->obtenerPorID($idPedido);

        /*Antes de aprobar el pedido, tomamos el id del usuario en sesion, para luego
        manejar el hecho de que un mismo usuario no pueda aprobar su propio pedido*/
        $idUsuarioSesion = session()->get('id_usuario');
        
        //Se verifica que el usuario en sesion no sea el mismo que creo el pedido
        if ($idUsuarioSesion === $pedido->obtenerUsuario()->obtenerID()) {

            throw new \InvalidArgumentException(
                'No puede aprobar un pedido creado por usted mismo.'
            );
        }

        // Verificacion que se hace si no existe el objeto
        if ($pedido === null) {
            throw new \InvalidArgumentException('Pedido no encontrado.');
        }

        // el estado == 1 corresponde al estado "Pendiente". si no esta en este estado
        // significa que ya fue aceptado o rechazado
        if ($pedido->obtenerEstado()->obtenerID() !== 1) {
            throw new \InvalidArgumentException("El pedido se encuentra en un estado invalido para su aprobación.");
        }

        return $this->pedidoModel->aprobar($pedido->obtenerID());
    }

    /* Método que crea un pedido junto con sus respectivos detalles de pedido
    pasando por parametro sus datos
    */ 
    public function crearPedido (int $idServicio, ?string $comentario): int
    {
        //Primero se obtiene el usuario en sesion que es el que crea el pedido
        $idUsuario = session()->get('id_usuario');

        //Se obtiene la fecha actual, que es en la que se realiza el pedido
        $fechaSolicitud = date('Y-m-d');

        //Se asigna por defecto el estado del pedido como "Pendiente"
        $idEstado = 1;

        //Hace uso del metodo del modelo para crear un pedido
        return $this->pedidoModel->agregar(
            $fechaSolicitud,
            $comentario,
            $idEstado,
            $idServicio,
            $idUsuario
        );
    }
}