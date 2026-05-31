<?php

namespace App\Services;

use App\Models\TipoProductoModel;

class TipoProductoService
{
    //Variable a utilizar que hace referncia al modelo
    protected TipoProductoModel $tipoProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->tipoProductoModel = new TipoProductoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los tipos de productos y ser utilizados en el dropdown*/
    public function obtenerTiposDropdown(): array
    {
        $tipos = [];

        foreach ($this->tipoProductoModel->obtenerTodos() as $tipo_producto) {
            $tipos[$tipo_producto->obtenerID()] =
                $tipo_producto->obtenerNombre();
        }

        return $tipos;
    }
}