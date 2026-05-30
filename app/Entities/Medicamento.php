<?php

namespace App\Entities;

class Medicamento 
{
    private int $id;
    private string $nombre;
    private bool $activo;

    public function __construct(int $id, string $nombre, bool $activo){
        $this->asignarID($id);
        $this->cambiarNombre($nombre);
        $this->cambiarActivo($activo);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    public function cambiarNombre(string $nombre) : void
    {
        $this->nombre = $nombre;
    }

    public function cambiarActivo(bool $activo) : void
    {
        $this->activo = $activo;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerNombre() : string
    {
        return $this->nombre;
    }

    public function obtenerActivo() : bool
    {
        return $this->activo;
    }
}