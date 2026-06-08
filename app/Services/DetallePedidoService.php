<?php

namespace App\Services;

use App\Models\DetallePedidoModel;
use App\Services\PedidoService;

class DetallePedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected DetallePedidoModel $detalleModel;
    protected PedidoService $pedidoService;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->detalleModel = new DetallePedidoModel();//Se reconoce e instancia la clase
        $this->pedidoService = new PedidoService();//Se reconoce e instancia la clase
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

    private function crearDetallesPedidos(array $detalles ) : void{
        //Se muestran mensajes de error en caso de que alguno de los siguientes campos este vacio
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

        //Se crean todos los detalles asociados al pedido que se está creando, haciendo uso del metodo del model
        foreach ($detalles as $detalle) { 
            $this->detalleModel->crearDetallePedido( 
                $detalle['cantidad_medicamento'], 
                $idPedido, 
                $detalle['id_proveedor'], 
                $detalle['id_producto'] ); 
        } 
    }

    public function crearPedidoCompleto (array $datos) : void {
        $this->pedidoService->crearPedido();
        $this->crearDetallesPedidos($datos);
    }    
}