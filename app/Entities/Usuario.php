<?php

namespace App\Entities;

class Usuario 
{
    private int $id;
    private string $dni;
    private string $nombre;
    private string $apellido;
    private string $email;
    private string $password;
    private bool $activo;
    private Rol $id_rol;

    public function __construct(int $id, string $dni, string $nombre, string $apellido, string $email, string $password, bool $activo, Rol $id_rol){
        $this->asignarID($id);
        $this->asignarDni($dni);
        $this->asignarNombre($nombre);
        $this->asignarApellido($apellido);
        $this->asignarEmail($email);
        $this->asignarPassword($password);
        $this->asignarActivo($activo);
        $this->asignarRol($id_rol);
    }

    private function asignarID(int $id) : void
    {
        $this->id = $id;
    }

    private function asignarDni(string $dni) : void
    {
        $this->dni = $dni;
    }

    private function asignarNombre(string $nombre) : void
    {
        $this->nombre = $nombre;
    }

    private function asignarApellido(string $apellido) : void
    {
        $this->apellido = $apellido;
    }

    private function asignarEmail(string $email) : void
    {
        $this->email = $email;
    }

    private function asignarPassword(string $password) : void
    {
        $this->password = $password;
    }

    private function asignarActivo(bool $activo) : void
    {
        $this->activo = $activo;
    }

    private function asignarRol(Rol $id_rol) : void
    {
        $this->id_rol = $id_rol;
    }

    public function obtenerID() : int
    {
        return $this->id;
    }

    public function obtenerDni() : string
    {
        return $this->dni;
    }

    public function obtenerNombre() : string
    {
        return $this->nombre;
    }

    public function obtenerApellido() : string
    {
        return $this->apellido;
    }

    public function obtenerNombreCompleto() : string
    {
        return $this->apellido . ' ' . $this->nombre;
    }

    public function obtenerEmail() : string
    {
        return $this->email;
    }

    public function obtenerPassword() : string
    {
        return $this->password;
    }

    public function obtenerActivo() : bool
    {
        return $this->activo;
    }

    public function obtenerRol() : Rol
    {
        return $this->id_rol;
    }
}