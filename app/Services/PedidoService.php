<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;

class PedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected PedidoModel $pedidoModel;
    protected DetallePedidoModel $detallePedidoModel;
    protected $db;//Variable a utilizar para realizar la conexion a la BD.

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();//Se reconoce e instancia la clase
        $this->detallePedidoModel = new DetallePedidoModel();//Se reconoce e instancia la clase
        $this->db = \Config\Database::connect();//Conexion con nuestra BD.
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
    public function crearPedido( int $idServicio, ?string $comentario, array $detalles ): int 
    {
        //Primero se controla que se haya creado al menos un detalle de pedido para continuar
        if (empty($detalles)) {
            throw new \Exception('Debe agregar al menos un detalle al pedido.');
        }

        //Luego se obtiene el id del usuario en sesión para asignar a dicho usuario el pedido
        $idUsuario = session()->get('id_usuario'); 
        
        //Luego se obtiene la fecha actual, que es en la que se realiza el pedido
        $fechaSolicitud = date('Y-m-d'); 
        
        //Se establece el estado del pedido. Por defecto todo pedido nuevo está en "pendiente"
        $idEstado = 1; 
        
        //Se hace uso de transacción para evitar que queden peddidos inconmpletos en caso de errores en el medio
        $this->db->transStart();
        
        //Ahora si se crea el pedido y se guarda su id. Se hace uso del metodo del model
        $idPedido = $this->pedidoModel->agregar($fechaSolicitud, $comentario, $idEstado, $idServicio, $idUsuario ); 

    

        //Se finaliza la transacción una vez creado el pedido y sus detalles
        $this->db->transComplete(); 
        
        //En caso de errores, los manejamos con un excepcion
        if (!$this->db->transStatus()){ 
            throw new \Exception('Ocurrió un error al crear el pedido.'); 
        } 
        
        //Por utlimo retornamos el id del nuevo pedido creado
        return $idPedido; 
    }
}