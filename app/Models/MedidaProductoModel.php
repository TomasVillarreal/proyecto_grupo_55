<?php

namespace App\Models;

use CodeIgniter\Model;

class MedidaProductoModel extends Model
{
    protected $table = 'medida_producto';//El nombre de la tabla en nuestra bd
    protected $primaryKey = 'id_medida_producto'; //Identificador único
    protected $allowedFields = ['nombre_medida']; //Columna de la tabla
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Formato de dato a devolver

    /*Se crea un método para obtener todos las medidas de las dosis de los productos ordenados alfabeticamente,
    lo cual va a ser útil para cargar en el formulario.
    Asocia a cada ID su nombre de medida y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador*/
    
    public function obtenerParaDropdown(): array
    {
        $medidas = $this->orderBy('nombre_medida', 'ASC')->findAll();
        $opciones_medidas = [];
        foreach ($medidas as $medida) {
            $opciones_medidas[$medida->id_medida_producto] = $medida->nombre_medida;
        }
        return $opciones_medidas;
    }
}