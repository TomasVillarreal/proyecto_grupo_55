<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Usuario;
use App\Entities\Rol;
class UsuarioModel extends Model{
    protected $table = 'usuario'; //El nombre de la tabla
    protected $primaryKey = 'id_usuario'; //Clave primaria el id de usuario
    protected $allowedFields = ['dni_usuario','nombre_usuario','apellido_usuario','email_usuario','password_usuario','activo_usuario','id_rol'];//Las columnas de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente

     // Funcion que crea un objeto de la entidad usuario.
    private function crearObjeto(array $registro): Usuario
    {
        $rol = new Rol(
            (int) $registro['id_rol'],
            $registro['nombre_rol'],
        );

        return new Usuario(
            (int) $registro['id_usuario'],
            (int)$registro['dni_usuario'],
            $registro['nombre_usuario'],
            $registro['apellido_usuario'],
            $registro['email_usuario'],
            $registro['password_usuario'],
            (bool) $registro['activo_usuario'],
            $rol
        );
    }

    /*Se crea un método para obtener la información de un usuario.
    Se piensa que sea utilizada para mostrar en la pantalla principal en algún lado.
    También puede ser utilizada posteriormente para mostrar la información de usuarios
    que hayan realizado pedidos y aún estén registrados en el sistema (activos).
    */
    public function obtenerInfoUsuario(string $email): ?Usuario
    {
        $builder = $this->db->table('Usuario u');//Se usa la tabla de usuario.
        $builder->select(
            'u.id_usuario, u.dni_usuario,
            u.nombre_usuario, u.apellido_usuario,
            u.email_usuario, u.password_usuario, r.id_rol, r.nombre_rol, u.activo_usuario');
        $builder->join('Rol r', 'r.id_rol = u.id_rol');//Se hace el JOIN con la tabla Rol para obtener el id del tipo de rol.
        $builder->where('u.email_usuario', $email);//Se filtra por el email del usuario.
        $builder->where('u.activo_usuario', 1);//Se filtra por usuarios activos.

        //Se obtienen los usuarios activos y su información
        $registro = $builder->get()->getRowArray();

        if ($registro === null) {
            return null;
        }

        return $this->crearObjeto($registro);
    }

    /*
    Método que verifica si un usuario ya existe en el sistema o no
    */
    public function existeUsuario(string $email, int $dni): bool
    {
         //Ejecuta el procedimiento almacenado
        $query = $this->db->query(
            "CALL sp_existe_usuario(?, ?)",
            [$email, $dni]
        );

        //Obtiene la fila devuelta por el procedimiento
        $resultado = $query->getRow();

        //Libera el resultado inmediatamente para mantener la conexión limpia
        $query->freeResult();

        //Si $resultado no es null y tiene un id_usuario válido (mayor a 0), significa que el usuario YA existe.
        return isset($resultado->id_usuario) && (int)$resultado->id_usuario > 0;
    }

    /*
    Funcion que permite crear un nuevo usuario con los datos necesarios para su 
    creacion pasados como parametro. Retorna el id del nuevo usuario creado.
    */
    public function agregar(int $dni, string $nombre, string $apellido, string $email, string $password, int $rol) : int
    {
        $this->insert([
            'dni_usuario' => $dni,
            'nombre_usuario' => $nombre,
            'apellido_usuario' => $apellido,
            'email_usuario' => $email,
            'password_usuario' => $password,
            'activo_usuario' => 1,
            'id_rol' => $rol
        ]);
        return $this->insertID();
    }
}