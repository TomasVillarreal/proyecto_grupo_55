<?php

namespace App\Services;

use App\Models\DetallePedidoModel;
use App\Services\PedidoService;
use CodeIgniter\Database\BaseConnection;

class DetallePedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected DetallePedidoModel $detalleModel;
    protected PedidoService $pedidoService;
    protected BaseConnection $db;//Variable a utilizar para realizar la conexion a la BD.


    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->detalleModel = new DetallePedidoModel();//Se reconoce e instancia la clase
        $this->pedidoService = new PedidoService();//Se reconoce e instancia la clase
        $this->db = \Config\Database::connect();//Conexion con nuestra BD.
    }

    /*Metodo para obtener los detalles de un pedido particular, 
    haciendo uso del metodo del modelo, y devolviendolo*/
   public function obtenerDetallesPedido(int $idPedido): array
    {
        $detalles = $this->detalleModel->obtenerDetallesPorPedido($idPedido);

        $listadoDetalles = [];
        foreach ($detalles as $detalle) {
            $listadoDetalles[] = [
                'id_detalle'    => $detalle->obtenerID(),
                'cantidad'      => $detalle->obtenerCantidad(),
                'proveedor'     => $detalle->obtenerProveedor()->obtenerNombre(),
                'medicamento'   => $detalle->obtenerProducto()->obtenerMedicamento()->obtenerNombre(),
                'tipo'          => $detalle->obtenerProducto()->obtenerTipo()->obtenerNombre(),
                'medida'        => $detalle->obtenerProducto()->obtenerUnidadMedida()->obtenerNombre(),
                'dosis'         => $detalle->obtenerProducto()->obtenerDosis(),
                'descripcion'   => $detalle->obtenerProducto()->obtenerDescripcion(),
            ];
        }
        return $listadoDetalles;
    }

    /*
    Metodo que realiza la validacion de los detalles del pedido,los cuales son
    pasados por parametro como un array. Dicho metodo se utiliza por los siguientes 
    metodos, previo a la creacion del pedido.
    */
    public function validarDetalles(array $detalles): void
    {
        if (empty($detalles)) {
            throw new \Exception(
                'Debe agregar al menos un medicamento al pedido.'
            );
        }

        foreach ($detalles as $detalle) {

            if (empty($detalle['id_producto'])) {
                throw new \Exception(
                    'Todos los detalles deben tener un producto farmacéutico seleccionado.'
                );
            }

            if (empty($detalle['id_proveedor'])) {
                throw new \Exception(
                    'Todos los detalles deben tener un proveedor seleccionado.'
                );
            }

            if (empty($detalle['cantidad_medicamento'])) {
                throw new \Exception(
                    'Todos los detalles deben indicar una cantidad.'
                );
            }
        }
    }   

    /*
    Metodo que realiza la creacion de los detalles de los pedidos, pasando
    por parametro el id del pedido al cual pertenecen los detalles y los
    datos de los detalles.
    */
    public function crearDetallesPedidos(int $idPedido, array $detalles): void
    {
        foreach ($detalles as $detalle) {

            $this->detalleModel->agregar(
                $detalle['cantidad_medicamento'],
                $idPedido,
                $detalle['id_proveedor'],
                $detalle['id_producto']
            );
        }
    }

    /*
    Metodo que crea un pedido completo, con sus respectivos datos,
    de cada detalle que posea.
    */
    public function crearPedidoCompleto(int $idServicio, ?string $comentario, array $detalles): int
    {
        //Primero se realizan las validaciones de los detalles del pedido
        $this->validarDetalles($detalles);

        //Una vez validados los datos de los detalles, se comienza la transaccion
        $this->db->transStart();

        //Se crea un pedido y se guarda su id
        $idPedido = $this->pedidoService->crearPedido($idServicio, $comentario);

        //Se crean los detalles del pedido
        $this->crearDetallesPedidos($idPedido, $detalles);

        //Finaliza la transaccion
        $this->db->transComplete();

        //Control de errores
        if (!$this->db->transStatus()) {
            throw new \Exception(
                'Ocurrió un error al crear el pedido.'
            );
        }

        //Retorna el id del pedido creado
        return $idPedido;
    }
}