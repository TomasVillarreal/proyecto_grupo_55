<?php

namespace App\Services;

use App\Models\DetallePedidoModel;

class DetallePedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected $detalleModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->detalleModel = model(DetallePedidoModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los detalles de un pedido particular, 
    haciendo uso del metodo del modelo, y devolviendolo*/
    public function obtenerDetallesPedido(int $id_pedido): array
    {
        $detalles = $this->detalleModel->obtenerDetallesPorPedido($id_pedido);

        if (!$detalles) {
            throw new \Exception("Pedido no encontrado");
        }

        return $detalles;
    }
}