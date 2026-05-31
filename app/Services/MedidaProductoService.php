<?php

namespace App\Services;

use App\Models\MedidaProductoModel;

class MedidaProductoService
{
    //Variable a utilizar que hace referncia al modelo
    protected MedidaProductoModel $medidaProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->medidaProductoModel = new MedidaProductoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener las medidas de los productos y luego utilizarlos en el dropdown*/
    public function obtenerMedidaDropdown(): array
    {
        $medidas = [];

        foreach ($this->medidaProductoModel->obtenerTodos() as $medida) {
            $medidas[$medida->obtenerID()] =
                $medida->obtenerNombre();
        }

        return $medidas;
    }
}