<?php

namespace App\Services;

use App\Models\ServicioMedicoModel;
use App\Libraries\CatalogoTemplate;

class ServicioMedicoService extends CatalogoTemplate
{
    //Variable a utilizar que hace referncia al modelo
    protected ServicioMedicoModel $servicioModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->servicioModel = new ServicioMedicoModel();//Se reconoce e instancia la clase
    }

    protected function obtenerOpciones() : array
    {
        return $this->servicioModel->obtenerTodos();
    }   
}