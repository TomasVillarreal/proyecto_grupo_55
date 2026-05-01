<?php

namespace App\Services;

use App\Models\EstadoPedidoModel;

class EstadoPedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected $estadoPedidoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->estadoPedidoModel = model(EstadoPedidoModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener las medidas de los productos y luego utilizarlos en el dropdown*/
    public function obtenerEstadosDropdown(): array
    {
        return $this->estadoPedidoModel->obtenerParaDropdown();
    }
}