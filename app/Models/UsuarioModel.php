<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model{
    protected $table = 'usuario'; //El nombre de la tabla
    protected $primaryKey = 'id_usuario'; //Clave primaria el id de usuario
    protected $allowedFields = ['dni_usuario','nombre_usuario','apellido_usuario','email_usuario','password_usuario','activo_usuario','id_rol'];//Las columnas de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Se especifica el tipo de dato a devolver


    /*Se crea un método para obtener la información de un usuario.
    Se piensa que sea utilizada para mostrar en la pantalla principal en algún lado.
    También puede ser utilizada posteriormente para mostrar la información de usuarios
    que hayan realizado pedidos y aún estén registrados en el sistema (activos).
    */
    public function obtenerInfoUsuario(string $email): ?object
    {
        $builder = $this->db->table('Usuario u');//Se usa la tabla de usuario.
        $builder->select(
            'u.id_usuario, u.dni_usuario,
            CONCAT(u.apellido_usuario, " ", u.nombre_usuario) AS nombre_completo,
            u.email_usuario, u.password_usuario, r.id_rol, r.nombre_rol');
        $builder->join('Rol r', 'r.id_rol = u.id_rol');//Se hace el JOIN con la tabla Rol para obtener el id del tipo de rol.
        $builder->where('u.email_usuario', $email);//Se filtra por el email del usuario.
        $builder->where('u.activo_usuario', 1);//Se filtra por usuarios activos.

        //Se obtienen los usuarios activos y su información
        return $builder->get()->getRow();
    }

    /*
    Método que verifica si un usuario ya existe en el sistema o no
    */
    public function existeUsuario(string $email, int $dni): bool
    {
        $builder = $this->db->table('Usuario');//Se busca en la tabla de usuario.
        $builder->groupStart();//Para poder hacer doble 'where' 
            $builder->where('email_usuario', $email); //Filtro por email
            $builder->orWhere('dni_usuario', $dni); //Filtro por dni
        $builder->groupEnd();
        //Se verifica si existe al menos un resultado
        return $builder->countAllResults() > 0;
        
    }

    /*Se crea un método para obtener la información de un usuario y presentarla
    como su perfil en el sidebar del home
    */
    public function obtenerInfoParaPerfil(string $email): ?object
    {
        $builder = $this->db->table('Usuario u');//Se usa la tabla de usuario.
        $builder->select(
            'CONCAT(u.apellido_usuario, " ", u.nombre_usuario) AS nombre_completo,
            u.email_usuario, r.nombre_rol');
        $builder->join('Rol r', 'r.id_rol = u.id_rol');//Se hace el JOIN con la tabla Rol para obtener el id del tipo de rol.
        $builder->where('u.email_usuario', $email);//Se filtra por el email del usuario.
        $builder->where('u.activo_usuario', 1);//Se filtra por usuarios activos.

        //Se obtienen información para el perfil del usuario
        return $builder->get()->getRow();
    }
}