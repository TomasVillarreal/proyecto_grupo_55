<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model{
    protected $table = 'rol'; //El nombre de la tabla
    protected $primaryKey = 'id_rol'; //Clave primaria el id del rol
    protected $allowedFields = ['id_rol', 'nombre_rol'];//Las columnas de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Se especifica el tipo de dato a devolver

    /*Se crea un método para obtener los roles de los usuarios,
    "Responsable" y "No responsable", lo cual se va a mostrar
    en la vista de creacion de usuarios.
    Se obtiene de forma descendente solo para que aparezca
    responsable primero.
    Se hace uso del formato clave valor como en otros models
    para que sea mas facil llamarlo en el form de usuarios*/
    
    public function obtenerParaDropdown(): array
    {
        $rol = $this->orderBy('nombre_rol', 'DESC')->findAll();
        $tiposRoles = [];
        foreach ($rol as $roles) {
            $tiposRoles[$roles->id_rol] = $roles->nombre_rol;
        }
        return $tiposRoles;
    }
}