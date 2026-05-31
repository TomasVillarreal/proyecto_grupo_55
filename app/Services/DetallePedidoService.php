<?php

namespace App\Services;

use App\Models\DetallePedidoModel;

class DetallePedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected DetallePedidoModel $detalleModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->detalleModel = new DetallePedidoModel();//Se reconoce e instancia la clase
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
}