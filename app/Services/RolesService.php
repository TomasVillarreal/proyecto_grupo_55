<?php

namespace App\Services;

use App\Models\RolesModel;
use Override;

class RolesService extends CatalogoService{
    protected RolesModel $rolesModel;//Variable que hace referencia al modelo

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->rolesModel = model(RolesModel::class);//Se reconoce e instancia la clase
    }

    #[Override]
    protected function obtenerOpciones(): array
    {
        return $this->rolesModel->obtenerTodos();
    }
} 