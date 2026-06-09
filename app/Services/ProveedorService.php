<?php

namespace App\Services;

use App\Models\ProveedorModel;
use App\Libraries\CatalogoTemplate;
use Override;

class ProveedorService extends CatalogoTemplate
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