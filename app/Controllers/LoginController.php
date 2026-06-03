<?php

namespace App\Controllers;

use App\Services\UsuarioService;

class LoginController extends BaseController{
     //Se crea la variable a utilizar del servicio del usuario
    protected $usuarioService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->usuarioService = new UsuarioService();
    }

    /*
    Método que carga la vista del login
    */
    public function vista_login(){
        return view('access/login', [
            'title' => 'Iniciar Sesion - Clinicks',
            'content' => view('access/login')]);
    }

    /*
    Método para manejar y mostrar los posibles errores en el inicio de sesion
    */
    public function erroresCreacionUsuario(string $email, string $password)
    {
        $errores = [];

        try {

            //Valida el ingreso del usuario al sistema verificando las credenciales
            $this->usuarioService->validarIngreso($email, $password);

        } catch (\Exception $e) {

            $errores['email_usuario'] = $e->getMessage();
            $errores['password_usuario'] = $e->getMessage();
        }

        //En caso de que hayan campos vacios
        if (!empty($errores)) {

            return redirect()->back()->withInput()->with('errores', $errores);
        }
    }

    /*
    Método que realiza la autenticación del email y del password del usuario.
    Hace uso de uno de los metodos del Usuario service para verificar que
    dicho email y contraseñas correspondan a un usuario en el sistema.
    */
    public function login()
    {
        //Primero se toman los datos POST del form
        $email = trim($this->request->getPost('email_usuario'));
        $password = $this->request->getPost('password_usuario');

        $resultado = $this->erroresCreacionUsuario($email, $password);

        if ($resultado) {
            return $resultado;
        }

        //Usando el metodo del service se valida el ingreso
        $usuario = $this->usuarioService->validarIngreso($email,$password);

        //En caso de que se validen correctamente se procede a crear la sesion del usuario, con sus datos
        session()->set([
            'id_usuario' => $usuario->id_usuario,
            'email_usuario' => $usuario->email_usuario,
            'nombre_rol' => $usuario->nombre_rol,
            'nombre_completo' => $usuario->apellido_usuario . ' ' . $usuario->nombre_usuario,
            'logged_in' => true
        ]);

        return redirect()->to('/');
    }

    /*
    Se crea un método que permite que un usuario se pueda desloggear para asi
    cerrar su sesion y permitir que otro ingrese
    */
    public function logout()
    {
        //Primero se cierra la sesion en curso
        session()->destroy();

        //Luego se redirige al usuario al login
        return redirect()->to('/access/login')->with('success', 'Sesión cerrada correctamente.');
    }
}