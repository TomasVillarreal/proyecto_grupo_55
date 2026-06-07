<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Rol;

class RolesModel extends Model{
    protected $table = 'rol'; //El nombre de la tabla
    protected $primaryKey = 'id_rol'; //Clave primaria el id del rol
    protected $allowedFields = ['id_rol', 'nombre_rol'];//Las columnas de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    
    // Funcion que crea un objeto de la entidad Rol.
    private function crearObjeto(array $registro): Rol
    {
        return new Rol(
            (int) $registro['id_rol'],
            $registro['nombre_rol'],
        );
    }

        /*Se crea un método para obtener los roles de los usuarios,
        "Responsable" y "No responsable", lo cual se va a mostrar
        en la vista de creacion de usuarios.*/
    public function obtenerTodos(): array
    {
        $registros = $this->orderBy('nombre_rol', 'ASC')->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }


}