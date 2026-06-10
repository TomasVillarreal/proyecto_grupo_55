<?php

namespace App\Services;

use App\Models\MedidaProductoModel;
use App\Libraries\CatalogoTemplate;
use Override;

class MedidaProductoService extends CatalogoTemplate
{
    //Variable a utilizar que hace referncia al modelo
    protected MedidaProductoModel $medidaProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->medidaProductoModel = new MedidaProductoModel();//Se reconoce e instancia la clase
    }

    #[Override]
    protected function obtenerOpciones(): array
    {
        return $this->medidaProductoModel->obtenerTodos();
    }
}