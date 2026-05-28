<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use App\Services\RolesService;

class UsuarioController extends BaseController{

    //Se crea la variable del servicio de usuario a utilizar
    protected $usuarioService;
    protected $rolesService;

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
    public function vista_crear_usuario(): string
    {
        //Se obtienen los roles
        $roles = $this->rolesService->obtenerRolesParaDropdown();

        return view('layout/main_layout', [
            'title' => 'Usuarios - Clinicks',
            'content' => view('usuarios/creacion_usuarios',[
                'roles' => $roles
            ])
        ]);
    }

    /*
    Se crea un metodo que cree a un usuario, haciendo uso de las validaciones
    de los servicios
    */
    public function crear_usuario()
    {
        //Se obtienen todos los POST del form
        $dataUsuario = $this->request->getPost(); 

        //Se pasa al service los datos del POST
        $this->usuarioService->crearUsuario( 
            $dataUsuario['dni_usuario'], 
            $dataUsuario['nombre_usuario'], 
            $dataUsuario['apellido_usuario'], 
            $dataUsuario['email_usuario'],
            $dataUsuario['password_usuario'],
            $dataUsuario['id_rol']); 
        
        //Se redirije al login para comprobar su correcta creacion
        return redirect()->to('/login');
    }

    /*
    Se crea un metodo que obtiene la informacion del usuario
    para poder mostrarse en forma de "perfil de usuario" en
    el sidebar
    */
    public function perfil_usuario(string $email)
    {
        return $this->usuarioService->obtenerInfoParaPerfil($email);
    }

}