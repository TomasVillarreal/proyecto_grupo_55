<?php

namespace App\Services;

use App\Models\MedicamentoModel;
use App\Services\ProductoFarmaceuticoService;
use CodeIgniter\Database\Exceptions\DatabaseException;
use InvalidArgumentException;

class MedicamentoService
{
    protected $medicamentoModel;//Variable a utilizar que hace referencia al modelo
    protected $productoService;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion.
    En POO nos enseñaron que el constructor tenia que tener el mismo nombre de la clase
    pero PHP exije que sea asi.*/
    public function __construct()
    {
        $this->medicamentoModel = model(MedicamentoModel::class);//Se reconoce e instancia la clase
        $this->productoService = new ProductoFarmaceuticoService();
    }

    /*Se crea un método para validar el nombre de un medicamento según nuestras reglas
    de negocio
    En caso de que cumpla con las validaciones sigue adelante (pq no hay errores),
    caso contrario tira una excepcion que sera agarrada por el controller y mostrada*/

    private function validarNombreMedicamento(string $medicamento) : void
    {
        $medicamento = trim($medicamento);//Se quitan espacios vacios

        //mb_strlen identifica cantidad de carcteres teniendo en cuenta las tildes, cosa que no hace strlen.
        if(mb_strlen($medicamento) < 3){
            throw new \InvalidArgumentException("El nombre del medicamento es muy corto. Debe contener al menos 3 letras o caracteres");
        }

        // esto deshabilita la posibilidad de que el nombre del medicamento posea unicamente numeros
        if(preg_match('/^\d+$/', $medicamento)){
            throw new \InvalidArgumentException(
                "El nombre del medicamento no puede contener únicamente números"
            );
        }
        //preg_match controla que no se ingresen al final cosas como #, ^, palabra y muchos espacios y un nro,etc
        if(!preg_match('/^[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ]+( [A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/u', $medicamento)){
            throw new \InvalidArgumentException("El nombre del medicamento no debe contener #, ^, espacios al inicio o al final, ni doble espacio");
        }
    }

    private function buscarMedicamentoPorNombre(string $nombre): ?object
    {
        return $this->medicamentoModel
            ->where('nombre_medicamento', $nombre)
            ->first();
    }

    public function buscarMedicamentoPorID(int $id): ?object
    {
        return $this->medicamentoModel
            ->where('id_medicamento', $id)
            ->first();
    }

    private function reactivarMedicamento(object $medicamento) : void
    {
        $this->medicamentoModel->update(
            $medicamento->id_medicamento,
            ['activo_medicamento' => 1]
        );
    }

    // Funcion que normaliza el nombre del medicamento a ingresar
    private function normalizarNombreMedicamento(string $nombre) : string{
        // ucfirst capitaliza el string, y strtolower convierte todo el string en minusculas
        return ucfirst(strtolower(trim($nombre)));
    }


    // Funcion que hace la insercion del medicamento en la bd
    private function insertarMedicamento(string $nombre) : int
    {
        // hace la insercion del med
        $idNuevo = $this->medicamentoModel->insert(['nombre_medicamento' => $nombre]);

        // Verificamos que se haya hecho la insercion
        if(!$idNuevo){
            throw new \RuntimeException('No se pudo crear el medicamento');
        }

        //Retorna el id del nuevo medicamento creado
        return (int) $idNuevo;
    }

    /*Se crea un metodo que creará un nuevo medicamento teniendo en cuenta que cumple con las validaciones.
    Retorna el ID del nuevo medicamento o lanza un exception*/
    public function crearMedicamento(string $nombreMedicamento): int
    {
        // normalizamos el nombre de medicamento a ingresar
        $nombreMedicamento = $this->normalizarNombreMedicamento($nombreMedicamento);

        // Luego validamos el formato del nombre usando el método anterior (osea validar nombrMedicamento)
        $this->validarNombreMedicamento($nombreMedicamento);

        //Buscamos que exista el medicamento (no importa si está activo o no aún)
        $medicamento = $this->buscarMedicamentoPorNombre($nombreMedicamento);
        if ($medicamento !== null) 
        {
            if ($medicamento->activo_medicamento === 1) 
            {
                throw new \InvalidArgumentException(
                    "Ya existe un medicamento activo con ese nombre."
                );
            }

            $this->reactivarMedicamento($medicamento);
            return (int) $medicamento->id_medicamento;
        }

        return $this->insertarMedicamento($nombreMedicamento);
    }

    private function modificacionMedicamento(int $id, string $nombre): bool{
        return $this->medicamentoModel->update($id, ['nombre_medicamento' => $nombre]);
    }

    /*Se crea un metodo que se utilizara para editar un medicamento (el nombre), siempre y cuando cumpla,
    con las validaciones*/
    public function modificarMedicamento(int $idMedicamento, string $nombreMedicamento): bool
    {
        $medicamento = $this->buscarMedicamentoPorID($idMedicamento);//Primero se busca el id del medicamento

        if ($medicamento === null) {
            throw new \InvalidArgumentException('El medicamento no existe.');
        }

        // normalizamos y luego validamos que el nombre sea correcto
        $nombreMedicamento = $this->normalizarNombreMedicamento($nombreMedicamento);
        $this->validarNombreMedicamento($nombreMedicamento);

        //Si el nombre es igual a uno ya almacenado, devuelve false al controller
        if ($medicamento->nombre_medicamento === $nombreMedicamento) {
            // Si entra aca no hubo cambios
            return false;
        }

        /*Se verifica que sea unico el medicamento, osea el nombre*/
        if($this->medicamentoModel->medicamentoUnico($nombreMedicamento, $idMedicamento)){
            throw new \InvalidArgumentException('Ya existe un medicamento con ese nombre');
        }

        return $this->modificacionMedicamento($idMedicamento, $nombreMedicamento);
    }

    private function desactivarMedicamento(int $id) : bool{
        return $this->medicamentoModel->update($id, ['activo_medicamento' => 0]);
    }

    /*Metodo para eliminar logicamente un medicamento*/
    public function eliminarMedicamento(int $idMedicamento): bool
    {
        // buscamos al medicamento
        $medicamento = $this->buscarMedicamentoPorID($idMedicamento);

        //si no existe o ya fue deshabilitado:
        if ($medicamento === null || !$medicamento->activo_medicamento) {
            throw new \InvalidArgumentException("El medicamento no existe o ya está inactivo.");
        }

        //Se elimina el medicamento
        $this->desactivarMedicamento($idMedicamento);

       //Se eliminan los productos farmaceuticos asociados a dicho medicamento
        return $this->productoService->eliminarProductosPorMedicamento($idMedicamento);
    }

    /*Se crea este metodo para facilitar la obtencion de la lista de medicamentos,
    asociando su ID con el nombre, presentandolos en array y asegurando de que estan
    correctamente escritos*/
    public function obtenerMedicamentosDropdown(): array
    {
        $medicamentos = $this->medicamentoModel->obtenerMedicamentosActivos();
        $listado = [];

        foreach($medicamentos as $medicamento){
            $listado[$medicamento->id_medicamento] = $medicamento->nombre_medicamento;
        }
        return $listado;
    }
}