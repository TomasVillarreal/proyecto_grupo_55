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

    /*Metodo para obtener las medidas de los productos y luego utilizarlos en el dropdown*/
    public function obtenerServiciosDropdown(): array
    {
        $opciones = [];

        foreach ($this->servicioModel->obtenerTodos() as $servicio) {
            $opciones[$servicio->obtenerID()] =
                $servicio->obtenerNombre();
        }

        return $opciones;
    }
}