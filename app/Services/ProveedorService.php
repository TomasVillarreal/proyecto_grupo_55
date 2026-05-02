<?php

namespace App\Services;

use App\Models\ProveedorModel;

class ProveedorService
{
    //Variable a utilizar que hace referncia al modelo
    protected $proveedorModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->proveedorModel = model(ProveedorModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los proveedores de los productos y luego utilizarlos en el dropdown*/
    public function obtenerProveedoresDropdown(): array
    {
        $proveedores = $this->proveedorModel->orderBy('nombre_proveedor', 'ASC')->findAll();
        $proveedores = [];
        foreach ($proveedores as $proveedor) {
            $listado[$proveedor->id_medida_producto] = $proveedor->nombre_medida;
        }
        return $proveedores;
    }
}