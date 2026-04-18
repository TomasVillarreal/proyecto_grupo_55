<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoProductoModel extends Model
{
    protected $table = 'tipo_producto'; //El nombre de nuestra tabla en bd
    protected $primaryKey = 'id_tipo_producto';
    protected $allowedFields = ['nombre_tipo_producto']; //La columna de la tabla
    protected $useTimestamps = false;
    protected $returnType = 'object';

    /*Se crea un método para obtener todos los tipos de productos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el formulario*/
    
    public function obtenerTiposProductos(): array
    {
        return $this->orderby('nombre_tipo_producto','ASC')->findAll();
    }

    /*Posible método a implementar, lo que busca es asociar un ID a un tipo en especifico
    lo que en teoría facilitaría la carga a los dropdowns pero no sé si conviene por si en
    un futuro hay modificaciones en los Ids, lo dejo igual por las dudas.

    ´public function obtenerParaDropdown(): array
    {
        $tipos = $this->orderBy('nombre_tipo_producto', 'ASC')->findAll();
        $options = [];
        foreach ($tipos as $tipo) {
            $options[$tipo->id_tipo_producto] = $tipo->nombre_tipo_producto;
        }
        return $options;
    }
    */
}

