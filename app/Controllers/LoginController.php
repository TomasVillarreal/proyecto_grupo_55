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
    Método que realiza la autenticación del email y del password del usuario.
    Hace uso de uno de los metodos del Usuario service para verificar que
    dicho email y contraseñas correspondan a un usuario en el sistema.
    */
    public function login()
    {
        //Primero se toman los datos POST del form
        $email = trim($this->request->getPost('email_usuario')); //Se le quitan los posibles espacios vacios
        $password = $this->request->getPost('password_usuario');

        //Se hace uso de un try-catch para controlar posibles errores
        try{
            //Usando el metodo del service se valida el ingreso
            $usuario = $this->usuarioService->validarIngreso($email,$password);

            //En caso de que se validen correctamente se procede a crear la sesion del usuario, con sus datos
            session()->set([
                'id_usuario' => $usuario->id_usuario,
                'email_usuario' => $usuario->email_usuario,
                'nombre_rol' => $usuario->nombre_rol,
                'logged_in' => true
            ]);

            return redirect()->to('/');

        }catch(\Exception $e){
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}