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
    Se crea un método que valida que el email pasado por parámetro.
    Acá se hace uso de la API Abstract Email Validation
     */
    public function validarEmailAPI(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /*
    Se crea un método que valida que el password pasado por parámetro esté bien
    escrito para luego poder ser aceptado.
     */
    public function validarPassword(string $password): bool
    {
        /*
        Se crea una variable que permite validar una contraseña, segun un formato correcto.
        Mínimo 8 caracteres (letras, números o símbolos comunes)
        */
        $patron_password = '/^(?=.*[A-Z])(?=.*[0-9])[A-Za-z0-9!@#$%^&*()_+=\-`~[\]{}|;\':\",.\/<>?]{8,}$/';

        //Se verifica que el password pasado como parametro cumpla con las condiciones del patron.
        if (!preg_match($patron_password, $password)) {
            throw new \InvalidArgumentException("La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula y al menos un número.");
        }

        //En caso de que la contraseña cumpla con las condiciones, devuelve true
        return true;
    }

    /*
    Se crea un método que valida que tanto el nombre como el apellido del usuario
    que son pasados por parametro están bien escrito para luego poder ser aceptados.
    */
    public function validarNombreCompleto(string $nombre, string $apellido): string|true
    {
        $nombre = trim($nombre);//Se quitan espacios vacios
        $apellido = trim($apellido);//Se quitan espacios vacios

        //mb_strlen identifica cantidad de carcteres teniendo en cuenta las tildes, cosa que no hace strlen.
        //Se verifica que cada uno tenga al menos 2 o 3 caracteres de forma individual.
        if(
            !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+( [A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/u', $nombre)
            ||
            !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+( [A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/u', $apellido)
        ){
            throw new \InvalidArgumentException(
                "El nombre o apellido no deben contener números ni símbolos."
            );
        }
        $nombreCompleto = $apellido . ' ' . $nombre; //Se concatena el nombre y el apellido para mejor uso
        return $nombreCompleto;
    }

    /*
    Se crea un método que valida el DNI del usuario para luego poder ser aceptado
    */
    public function validarDNI(string $dni): int|true
    {
        //Se definen dos variables que contemplan el DNI escrito con '.' y sin.
        $dniConPuntos = '/^[0-9]{1,2}(\.[0-9]{3}){2}$/';
        $dniSinPuntos = '/^[0-9]{7,8}$/';

        //Filtro de ambas posibilidades del dni
        if(preg_match($dniConPuntos, $dni) || preg_match($dniSinPuntos, $dni)){
            $dni = (int) str_replace('.','',$dni);//Se reemplazan los puntos por vacios para guardar correctamente en la BD.
        } else{
            //En caso de que el DNI no cumpla con el formato ya sea porque tiene letras o la longitud es incorrecta
            throw new \InvalidArgumentException("El formato del DNI no es válido. Ingrese entre 7 y 8 números (con o sin puntos).");
        }
        return true;
    }

    /*
    Se crea un método que valida que el usuario existe en el sistema
    y por ende se devuelve dicho usuario autenticado.
    */
    public function validarIngreso(string $email, string $password)
    {
        //Primero se obtiene la información del usuario que quiere ingresar
        //Ya en el metodo del modelo se verifica que sea un usuario activo
        $usuario = $this->obtenerUsuarioPorEmail($email);

        //Luego se verifica que el password ingresado, coincida con el almacenado en la BD.
        if(!$this->verificarHashPassword($password, $usuario->password_usuario)){
            throw new \Exception('Contraseña incorrecta.');
        }
    
        //En caso de cumplir con las validaciones, se retorna el usuario autenticado
        return $usuario;
    }   

    /*
    Se crea un método que compara el hash del password almacenado en BD,
    con el password, que se está ingresando, para verificar si es el
    mismo usuario.
    */
    protected function verificarHashPassword(string $password, string $passwordHasheado): bool
    {
        /*
        Haciendo uso de password_verify  (el cual hashea el primer parametro
        y luego lo compara con el passwordhasheado), se determina si el password
        ingresado es igual al almecenado en la BD.
        */
        return password_verify($password, $passwordHasheado);
    }

    /*
    Se crea un método que verifica que los datos principales y necesarios para la
    creacion de un nuevo usuario, son únicos y que por ende, no existe un usuario
    ya registrado con dicha información.
    */
    public function verificarUsuarioUnico(string $email, string $dni): bool
    {
        //Hace uso del metodo en el model para verificar la unicidad del usuario
        if($this->usuarioModel->existeUsuario($email, $dni)){
            throw new \InvalidArgumentException( 'El usuario ya existe en el sistema.' );
        }

        //En caso de que el usuario sea unico devuelve true
        return true;
    }

    /*
    Se crea un método que hashea el password del usuario para su correcto
    y seguro almacenamiento en nuestra BD.
    PASSWORD_DEFAULT ocupa un algoritmo bcrypt que hashea el password.
    */
    protected function hashearPassword(string $password): string
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashPassword;
    }

    /*
    Se crea un método que crea un usuario nuevo, de acuerdo a los datos
    pasados por parámetro, necesarios para dicha creación
    */
    public function crearUsuario(string $dni, string $nombre, string $apellido, string $email, string $password, int $rol): int 
    {
        //Se usa el metodo que realiza todas las validaciones
        $this->validacionCompleta($dni,$nombre,$apellido,$email,$password);

        //Se hashea el password
        $passwordHasheado = $this->hashearPassword($password);

        //Se crea un array con la data del nuevo usuario
        $dataUsuario = [
            'dni_usuario' => $dni,
            'nombre_usuario' => $nombre,
            'apellido_usuario' => $apellido,
            'email_usuario' => $email,
            'password_usuario' => $passwordHasheado,
            'id_rol' => $rol,
            'activo_usuario' => 1
        ];

            //Se inserta el nuevo usuario
            $idUsuarioNuevo = $this->usuarioModel->insert($dataUsuario);

            //Control de errores en caso de fallo en la nueva inserción
            if(!$idUsuarioNuevo){ 
                throw new \RuntimeException( 'No se pudo crear el usuario.'); 
            }
            //Retorna el ID del nuevo usuario creado 
            return (int)$idUsuarioNuevo;
    }

    /*
    Método que realiza todas las validaciones para pasar a la correcta creacion
    del usuario
    */
    public function validacionCompleta(string $dni, string $nombre, string $apellido, string $email, string $password)
    {    
        //Se cuemprueba que los campos sean validos usando los metodos del propio service
        if($this->validarDNI($dni) && $this->validarNombreCompleto($nombre, $apellido) &&
            $this->validarEmailAPI($email) && $this->validarPassword($password) &&
            $this->verificarUsuarioUnico($email, $dni)){

        } else {
            throw new \InvalidArgumentException('Los datos ingresados no son válidos.' );
        }
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
    public function obtenerIDUsuarioEnSesion(): ?int
    {
        $idUsuarioEnSesion = session()->get('id_usuario');

        //Se verifica que el id exista en sesión 
        if($idUsuarioEnSesion === null){ 
            return null; 
        } 

        //Se retorna el id casteado a entero
        return (int)$idUsuarioEnSesion;
    }
    /*
    Se crea un metodo que hace uso del metodo del model para obtener
    la info necesaria del usuario para mostrarla como su perfil
    */
    public function obtenerInfoParaPerfil(string $email): ?object
    {
        return $this->usuarioModel->obtenerInfoParaPerfil($email);

    }
} 

/**
 *         //Se hace uso de un try-catch para manejar errores en caso de que la API se caiga
        try{
            /*Se crea un cliente para poder concectarse a la url de la API
            Es un cliente HTTP de CI4.
            Crea una instancia de CURL (librería php para hacer peticiones HTTP).
            */
            $client = \Config\Services::curlrequest();

            /*Acá se realiza la consulta HTTP a la API
            response hace una petición HTTP GET y la guarda.
            
            $response = $client->get(
                'https://emailvalidation.abstractapi.com/v1/', //Contiene la ruta de especifica del servicio (endpoint de la API)
                [
                    //Especifica que se estan enviado parametros GET
                    'query' => [
                        'api_key' => env('ABSTRACT_API_KEY'), //La key que brinda Abstract que está en el archivo .env porque es privada.
                        'email' => $email
                    ]
                ]
            );

            //Se decodifica la respuesta JSON obteniendo el cuerpo de la respuesta HTTP
            $data = json_decode($response->getBody(), true);

            //Se verifica si el formato del email es valido y además si es un email que realmente existe
            return $data['is_valid_format']['value'] ?? false && (($data['deliverability'] ?? '') === 'DELIVERABLE');

        }catch (\Exception $e){ 
            return false; 
        }
 */