<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoProductoModel extends Model
{
    protected $table = 'tipo_producto'; //El nombre de nuestra tabla en bd
    protected $primaryKey = 'id_tipo_producto'; //Identificador único
    protected $allowedFields = ['nombre_tipo_producto']; //La columna de la tabla
    protected $useTimestamps = false; //Para evitar asignaciones y guardados de fecha automaticamente.
    protected $returnType = 'object'; //Formato de dato a devolver

    /*Se crea un método para obtener todos los tipos de productos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el formulario.
    Asocia a cada ID su tipo de producto y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador*/

    public function obtenerParaDropdown(): array
    {
        $tipos = $this->orderBy('nombre_tipo_producto', 'ASC')->findAll();
        $opciones_tipos = [];
        foreach ($tipos as $tipo) {
            $opciones_tipos[$tipo->id_tipo_producto] = $tipo->nombre_tipo_producto;
        }
        return $opciones_tipos;
    }
}

