<?php

namespace App\Services;

use App\Models\EstadoPedidoModel;

class EstadoPedidoService
{
    //Variable a utilizar que hace referncia al modelo
    protected EstadoPedidoModel $estadoPedidoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->estadoPedidoModel = new EstadoPedidoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener las medidas de los productos y luego utilizarlos en el dropdown*/
    public function obtenerEstadosDropdown(): array
    {
        $opciones = [];

        foreach ($this->estadoPedidoModel->obtenerTodos() as $estado) {
            $opciones[$estado->obtenerID()] =
                $estado->obtenerNombre();
        }

        return $opciones;
    }
}