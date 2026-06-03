<?php

namespace App\Services;

abstract class CatalogoService{

    public function obtenerOpcionesDropdown(){
        $opciones = [];
        foreach ($this->obtenerOpciones() as $opcion) {
            $opciones[$opcion->obtenerID()] =
                $opcion->obtenerNombre();
        }

        return $opciones;
    }

    protected abstract function obtenerOpciones() : array;
}