<?php

namespace App\Services;

use App\Models\ProveedorModel;
use Override;

class ProveedorService extends CatalogoService
{
    //Variable a utilizar que hace referncia al modelo
    protected ProveedorModel $proveedorModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->proveedorModel = new ProveedorModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los proveedores de los productos y luego utilizarlos en el dropdown*/
    #[Override]
    protected function obtenerOpciones(): array
    {
        return $this->proveedorModel->obtenerTodos();
    }
}