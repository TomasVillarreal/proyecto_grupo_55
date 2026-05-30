<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Medicamento;

class MedicamentoModel extends Model
{
    protected $table = 'medicamento'; //La tabla de nuestra bd creada en phpmyadmin
    protected $primaryKey = 'id_medicamento'; //Identificador de la tabla
    protected $allowedFields = ['nombre_medicamento','activo_medicamento']; //Las columnas de la tabla
    protected $useTimestamps = false; //Para no rellenar columnas de tiempo automaticamente.
    
    private ?array $cacheMedicamentos = [];
    private bool $todosCargados = false;

    //Se crea un método para obtener todos los medicamentos activos en el sistema
    public function obtenerTodos(): array
    {
        if ($this->todosCargados) {
            return array_values($this->cacheMedicamentos);
        }

        $registros = $this->where('activo_medicamento',1) //Se realiza el filtro por el campo activo y su valor 1 (por defecto activo)
                    ->orderby ('nombre_medicamento', 'ASC') //Forma en la que se van a presentar los medicamentos
                    ->findAll();

        foreach ($registros as $registro) {
            if (isset($this->cacheMedicamentos[$registro['id_medicamento']])) {
                continue;
            }

            $medicamento = new Medicamento (
                $registro['id_medicamento'],
                $registro['nombre_medicamento'], 
                (bool) $registro['activo_medicamento']
            );

            $this->cacheMedicamentos[$medicamento->obtenerID()] = $medicamento;
        }

        $todosCargados = true;

        return array_values($this->cacheMedicamentos);
    }

    /*Se crea un método que verifica que el medicamento que se desea ingresar sea único, por su nombre
    Se realiza en esta capa porque es el primer filtro de datos previo a una inserción
    Las demas validaciones se encuentran en la capa de negocios (helper)
    
    *Con $builder se pueden construir consultas SQL de forma orientada a objetos, más segura y no en "crudo".
    *Con ?int $excludeId = null se permite que el param recibido sea int o null y se asigna null en caso de
    que no se pase nada como argumento*/

    public function medicamentoUnico(string $nombre, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('nombre_medicamento', $nombre);
        $builder->where('activo_medicamento', 1); //Solo busca entre los activos.
        if($excludeId != NULL){
            $builder->where('id_medicamento !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
        /*Booleano que verifica las coincidencias de lo solicitado en esta funcion y lo que hay en la BD
        En caso de devolver true indica que el nombre ya está ocupado, caso contrario con false.*/
    }
}