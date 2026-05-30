<?php

namespace App\Entities;

class Proveedor 
{
    private int $id;
    private string $nombre;

    public function __construct(int $id, string $nombre){
        $this->asignarID($id);
        $this->cambiarNombre($nombre);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    public function cambiarNombre(string $nombre) : void
    {
        $this->nombre = $nombre;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerNombre() : string
    {
        return $this->nombre;
    }
}