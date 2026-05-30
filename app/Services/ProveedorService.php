<?php

namespace App\Services;

use App\Models\ProveedorModel;

class ProveedorService
{
    //Variable a utilizar que hace referncia al modelo
    protected ProveedorModel $proveedorModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->proveedorModel = new ProveedorModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los proveedores de los productos y luego utilizarlos en el dropdown*/
    public function obtenerProveedoresDropdown(): array
    {
        $opciones = [];

        foreach ($this->proveedorModel->obtenerTodos() as $proveedor) {
            $opciones[$proveedor->obtenerID()] =
                $proveedor->obtenerNombre();
        }

        return $opciones;
    }
}