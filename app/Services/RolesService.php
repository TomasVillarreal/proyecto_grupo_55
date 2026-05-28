<?php

namespace App\Services;

use App\Models\RolesModel;

class RolesService{
    protected $rolesModel;//Variable que hace referencia al modelo

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->rolesModel = model(RolesModel::class);//Se reconoce e instancia la clase
    }

    /*
    Se crea un metodo que hace uso del model para obtener
    los roles y msotrarlos como dropdown en el form para 
    la creacion del usuario.
    */
    public function obtenerRolesParaDropdown(): array
    {
        //Se llama al metodo del modelo
        return $this->rolesModel->obtenerRolesParaDropdown();
    }
} 