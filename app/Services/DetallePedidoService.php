<?php

namespace App\Services;

use App\Models\DetallePedidoModel;
use App\Entities\Pedido;

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
    public function obtenerDetallesPedido(Pedido $pedido): array
    {
        $detalles = $this->detalleModel->obtenerDetallesPorPedido($pedido);

        if (!$detalles) {
            throw new \Exception("Pedido no encontrado");
        }

        return $detalles;
    }
}