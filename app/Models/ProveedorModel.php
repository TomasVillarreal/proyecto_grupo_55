<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table = 'proveedor';//Nuestra tabla en la bd
    protected $primaryKey = 'id_proveedor';
    protected $allowedFields = ['nombre_proveedor'];

    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    protected $returnType = 'object'; //Formato de dato a devolver

    /*Se crea un método para obtener todos los proveedores a los cuales se les puede realizar pedidos
    lo cual va a ser útil para cargar en el form de  creacion de pedidos.
    Asocia a cada ID su nombre de proveedor y es más fácil llamarlo en los formualrios
    ahorrando lógica en un controlador*/
    
    public function obtenerParaDropdown(): array
    {
        $proveedores = $this->orderBy('nombre_proveedor', 'ASC')->findAll();
        $opciones_proveedores = [];
        foreach ($proveedores as $proveedor) {
            $opciones_proveedores[$proveedor->id_proveedor] = $proveedor->nombre_proveedor;
        }
        return $opciones_proveedores;
    }
}