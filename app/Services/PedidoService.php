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
    public function obtenerPedidos(): array
    {
        return $this->pedidoModel->obtenerPedidos();
    }
}