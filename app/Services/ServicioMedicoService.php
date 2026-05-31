<?php

namespace App\Services;

use App\Models\ServicioMedicoModel;

class ServicioMedicoService
{
    //Variable a utilizar que hace referncia al modelo
    protected ServicioMedicoModel $servicioModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->servicioModel = new ServicioMedicoModel();//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los servicios medicos  y luego utilizarlos en el dropdown*/
    public function obtenerServiciosDropdown(): array
    {
        $servicios = [];

        foreach ($this->servicioModel->obtenerTodos() as $servicio) {
            $servicios[$servicio->obtenerID()] =
                $servicio->obtenerNombre();
        }

        return $servicios;
    }
}