<?php

namespace App\Services;

use App\Models\MedicamentoModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use InvalidArgumentException;

class MedicamentoService
{
    protected $medicamentoModel;//Variable a utilizar que hace referencia al modelo

    /*Creacion del constructor para evitar llamar al modelo en cada funcion.
    En POO nos enseñaron que el constructor tenia que tener el mismo nombre de la clase
    pero PHP exije que sea asi.*/
    public function __construct()
    {
        $this->medicamentoModel = model(MedicamentoModel::class);//Se reconoce e instancia la clase

    }

    /*Se crea un método para validar el nombre de un medicamento según nuestras reglas
    de negocio
    En caso de que cumpla con las validaciones devuelve true,
    caso contrario devuelve un string con la cadena que especifica el error.*/

    public function validarNombreMedicamento(string $medicamento): string|true
    {
        $medicamento = trim($medicamento);//Se quitan espacios vacios

        //mb_strlen identifica cantidad de carcteres teniendo en cuenta las tildes, cosa que no hace strlen.
        if(mb_strlen($medicamento) < 3){
            return "El nombre del medicamento es muy corto. Debe contener al menos 3 letras o caracteres";
        }

        //preg_match controla que no se ingresen al final cosas como #, ^, palabra y muchos espacios y un nro,etc
        if(!preg_match('/^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ]+( [A-Za-z0-9ÁÉÍÓÚáéíóúñÑ]+)*$/u', $medicamento)){
                return "El nombre del medicamento no debe contener #, ^, espacios al inicio o al final, ni doble espacio";
            }

        return true;
    }

    /*Se crea un metodo que creará un nuevo medicamento teniendo en cuenta que cumple con las validaciones.
    Retorna el ID del nuevo medicamento o lanza un exception*/
    public function crearMedicamento(string $nombreMedicamento): int
    {
        //Primero se valida el formato usando el método anterior
        $validacion = $this->validarNombreMedicamento($nombreMedicamento);
        if($validacion !== true){
            throw new \InvalidArgumentException($validacion);//Si hay un error muestra los errores formateados del metodo
        }

        //Se valida si el nombre del medicamento ya está en uso, utilizando el metodo de su clase
        if($this->medicamentoModel->medicamentoUnico($nombreMedicamento)){
            throw new \InvalidArgumentException("Ya existe un medicamento con ese nombre!");
        }

        //Se inserta el nuevo medicamento que cumple con las validaciones, en la BD
        $id = $this->medicamentoModel->insert(['nombre_medicamento' => $nombreMedicamento]);

        //En caso de que haya un error en la insercion en la BD.
        if(!$id){
            throw new DatabaseException('No se pudo crear el medicamento');
        }

        return $id; //Retorna el id del nuevo medicamento
    }

    /*Se crea un metodo que se utilizara para editar un medicamento, siempre y cuando cumpla,
    con las validaciones*/
    public function modificarMedicamento(int $idMedicamento, string $nombreMedicamento): bool
    {
        /*Primero verificamos que exista el medicamento que pasamos por param, haciendo uso de nuestro model.
        Se tiene en cuenta que no deberían aparecer medicamentos que no están activos pero se maneja por si 
        se nos pasa alguno*/
        $medicamento = $this->medicamentoModel->find($idMedicamento);
        if(!$medicamento){
            throw new \InvalidArgumentException('El medicamento no existe');
        }

        return true;
    }
}