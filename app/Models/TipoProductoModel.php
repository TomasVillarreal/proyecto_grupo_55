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

    private ?array $cacheTiposProducto = null;

    public function obtenerTodos(): array
    {
        if ($this->cacheTiposProducto !== null) {
            return $this->cacheTiposProducto;
        }

        $registros = $this->orderBy('nombre_tipo_producto', 'ASC')->findAll();

        $tipos = [];

        foreach ($registros as $registro) {
            $tipos[] = new TipoProducto(
                $registro['id_tipo_producto'],
                $registro['nombre_tipo_producto']
            );
        }

        $this->cacheTiposProducto = $tipos;

        return $this->cacheTiposProducto;
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

