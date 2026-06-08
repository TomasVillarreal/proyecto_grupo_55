<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use App\Services\RolesService;

class UsuarioController extends BaseController{

    //Se crea la variable del servicio de usuario a utilizar
    protected UsuarioService $usuarioService;
    protected RolesService $rolesService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->usuarioService = new UsuarioService();
        $this->rolesService = new RolesService();
    }

    /*
    Se crea un metodo que carga la vista de creacion del usuario
    */
    public function mostrarAltaUsuario(): string
    {
        //Se obtienen los roles
        $roles = $this->rolesService->obtenerOpcionesDropdown();

        return view('layout/main_layout', [
            'title' => 'Registrar Usuario - Clinicks',
            'content' => view('access/registrarUsuario',[
                'roles' => $roles
            ])
        ]);
    }

    /*
    Método para manejar y mostrar los posibles errores en la creación del usuario
    */
    public function erroresCreacionUsuario(int $dni, string $nombre, string $apellido, string $email, string $password, ?string $id_rol) 
    {
        $errores = [];

        try {
            //Valida el DNI del usuario
            $this->usuarioService->validarDNI($dni);

        } catch (\Exception $e) {
            $errores['dni_usuario'] = $e->getMessage();
        }

        try {
            //Valida el nombre y apellido del usuario
            $this->usuarioService->validarNombreCompleto($nombre, $apellido);

        } catch (\Exception $e) {
            $errores['nombre_usuario'] = $e->getMessage();
            $errores['apellido_usuario'] = $e->getMessage();
        }

        try {
            //Valida el email del usuario (formato)
            if (!$this->usuarioService->validarEmailAPI($email)) {
                $errores['email_usuario'] = 'El email no tiene un formato válido.';
            }

        } catch (\Exception $e) {
            $errores['email_usuario'] = $e->getMessage();
        }
        try {
            //Valida el email y DNI del usuario (que sean unicos en el sistema)
            $this->usuarioService->verificarUsuarioUnico($email, $dni);

        } catch (\Exception $e) {

            $errores['email_usuario'] = $e->getMessage();
            $errores['dni_usuario'] = $e->getMessage();
        }

        try {
            //Valida el password ingresado por el usuario
            $this->usuarioService->validarPassword($password);

        } catch (\Exception $e) {
            $errores['password_usuario'] = $e->getMessage();
        }
        try {
            //Valida que el usuario haya seleccionado un rol
            if (empty($id_rol) || $id_rol == -1) {
                throw new \Exception('Debe seleccionar un rol.');
            }
        } catch (\Exception $e) {
            $errores['id_rol'] = $e->getMessage();
        }
        //En caso de que hayan campos vacios
        if (!empty($errores)) {

            return redirect()->back()->withInput()->with('errores', $errores);
        }
    }

    /*
    Se crea un metodo que cree a un usuario, haciendo uso de las validaciones
    de los servicios
    */
    public function manejarCreacionUsuario()
    {
        //Se obtienen todos los POST del form
        $dataUsuario = $this->request->getPost(); 

        //Se llama al metodo que maneja los errores y se le pasa los datos recibidos por el POST
        $resultado = $this->erroresCreacionUsuario(
            $dataUsuario['dni_usuario'],  
            $dataUsuario['nombre_usuario'], 
            $dataUsuario['apellido_usuario'], 
            $dataUsuario['email_usuario'],
            $dataUsuario['password_usuario'],
            $dataUsuario['id_rol'] ?? null
        );

        /*
        Como el metodo de los errores tiene un return redirect, se debe almacenar los errores (en caso de que haya)
        en una variable y retornarla en este metodo para mostrar los mensajes de error (si los hay).
        */
        if ($resultado) {
            return $resultado;
        }

        //En caso de que no haya errores se pasa al service los datos del POST
        $this->usuarioService->crearUsuario( 
            $dataUsuario['dni_usuario'], 
            $dataUsuario['nombre_usuario'], 
            $dataUsuario['apellido_usuario'], 
            $dataUsuario['email_usuario'],
            $dataUsuario['password_usuario'],
            $dataUsuario['id_rol']
        );
        //Se destruye la sesión actual
        session()->destroy();   

        //Se redirije al login para comprobar su correcta creacion
        return redirect()->to('/access/login')->with('success', 'Usuario creado correctamente.');
    }

    /*
    Se crea un metodo que obtiene la informacion del usuario
    para poder mostrarse en forma de "perfil de usuario" en
    el sidebar
    */
    public function obtenerPerfil(string $email)
    {
        return $this->usuarioService->obtenerInfoParaPerfil($email);
    }
}