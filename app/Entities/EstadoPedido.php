<?php

namespace App\Entities;

class EstadoPedido 
{
    private int $id;
    private string $nombre;

    public function __construct(int $id, string $nombre){
        $this->asignarID($id);
        $this->asignarNombre($nombre);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    private function asignarNombre(string $nombre) : void
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