<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\TipoProducto;

class TipoProductoModel extends Model
{
    protected $table = 'tipo_producto'; //El nombre de nuestra tabla en bd
    protected $primaryKey = 'id_tipo_producto'; //Identificador único
    protected $allowedFields = ['nombre_tipo_producto']; //La columna de la tabla
    protected $useTimestamps = false; //Para evitar asignaciones y guardados de fecha automaticamente.

    // Funcion que crea un objeto de la entidad TipoProducto.
    private function crearObjeto(array $registro): TipoProducto
    {
        return new TipoProducto(
            (int) $registro['id_tipo_producto'],
            $registro['nombre_tipo_producto'],
        );
    }

    /* Funcion que obtiene todos los registros de la BD que son de la clase tipo_producto
    y para cada uno de estos registros va creando un objeto de la entidad TipoProducto*/
    public function obtenerTodos(): array
    {
        $registros = $this->orderBy('nombre_tipo_producto', 'ASC')->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }

    /*Se crea un método para obtener todos los tipos de productos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el formulario.
    Asocia a cada ID su tipo de producto y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador
    public function obtenerParaDropdown(): array
    {
        $opciones = [];

        foreach ($this->obtenerTodos() as $tipoProducto) {
            $opciones[$tipoProducto->obtenerID()] = $tipoProducto->obtenerNombre();
        }

        return
    }*/
}

