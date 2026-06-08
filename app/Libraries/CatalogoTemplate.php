<?php

namespace App\Libraries;

abstract class CatalogoTemplate{

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