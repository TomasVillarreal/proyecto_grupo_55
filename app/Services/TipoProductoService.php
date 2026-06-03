<?php

namespace App\Services;

use App\Models\TipoProductoModel;
use Override;

class TipoProductoService extends CatalogoService
{
    //Variable a utilizar que hace referncia al modelo
    protected TipoProductoModel $tipoProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->tipoProductoModel = new TipoProductoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los tipos de productos y ser utilizados en el dropdown*/
    #[Override]
    protected function obtenerOpciones(): array
    {
        return $this->tipoProductoModel->obtenerTodos();
    }
}