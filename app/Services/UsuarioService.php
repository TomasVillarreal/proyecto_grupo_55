<?php

namespace App\Services;

use App\Models\UsuarioModel;

class UsuarioService{
    protected $usuarioModel; //Variable que hace referencia al modelo

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->usuarioModel = model(UsuarioModel::class);//Se reconoce e instancia la clase
    }

    /*
    Se crea un método que valida que el email pasado por parámetro esté bien
    escrito para luego poder ser aceptado.
     */
    public function validarEmail(string $email)
    {

    }

    /*
    Se crea un método que valida que el password pasado por parámetro esté bien
    escrito para luego poder ser aceptado.
     */
    public function validarPassword(string $password)
    {

    }

    /*
    Se crea un método que valida que tanto el nombre como el apellido del usuario
    que son pasados por parametro están bien escrito para luego poder ser aceptados.
    */
    public function validarNombreCompleto(string $nombre, string $apellido)
    {

    }

    /*
    Se crea un método que valida el DNI del usuario para luego poder ser aceptado
    */
    public function validarDNI(int $dni)
    {

    }

    /*
    Se crea un método que valida que valida que el usuario existe en el sistema
    y por ende puede ingresar al mismo.
    Basicamente lo que permite el ingreso (el login)
    */
    public function validarIngreso(string $email, string $password)
    {

    }

    /*
    Se crea un método que compara el hash del password almacenado en BD,
    con el password, que se está ingresando, para verificar si es el
    mismo usuario.
    */
    public function verificarHashPassword(string $password)
    {
        $passwordHasheado = $this->hashearPassword($password);
        if(password_verify($password, $passwordHasheado)){
            return true;
        } else{
            throw new \Exception("Contraseña incorrecta");
        }
    }

    /*
    Se crea un método que verifica que los datos principales y necesarios para la
    creacion de un nuevo usuario, son únicos y que por ende, no existe un usuario
    ya registrado con dicha información.
    */
    public function verificarUsuarioUnico(string $email, int $dni)
    {

    }

    /*
    Se crea un método que hashea el password del usuario para su correcto
    y seguro almacenamiento en nuestra BD.
    PASSWORD_DEFAULT ocupa un algoritmo bcrypt que hashea el password.
    */
    public function hashearPassword(string $password): string
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashPassword;
    }

    /*
    Se crea un método que cree un usuario nuevo, de acuerdo a los datos
    pasados por parámetro, necesarios para dicha creación
    */
    public function crearUsuario(int $dni, string $nombre, string $apellido, string $email, string $password)
    {

    }

    /*
    Se crea un método que obtiene la información de los usuarios, llamando
    al método del model y pasando el email del usuario como parametro.
    */
    public function obtenerUsuarioPorEmail(string $email)//: array
    {
        $usuario = $this->usuarioModel->obtenerInfoUsuario($email);
        if(!$usuario){
            throw new \Exception("Usuario no encontrado");
        }
        return $usuario;
    }

    /*
    Se crea un método que obtiene el id de usuario en sesion, para su
    posterior uso junto con Pedidos.
    */
    public function obtenerIDUsuarioEnSesion(): int
    {
        $idUsuarioEnSesion = session()->get('id_usuario');
        return $idUsuarioEnSesion;
    }

    /*
    Se crea un método que compara el ID del usuario en sesion
    junto con el ID que se encontrara en los pedidos, para asi
    evitar que un mismo usuario cree un pedido y lo pueda
    aceptar el mismo.
    Capaz que este metodo tenga que ir en otro lado. A charlar.
    *
    public function verificarIDParaPedidos(int $id)//: int
    {

    }*/
} 