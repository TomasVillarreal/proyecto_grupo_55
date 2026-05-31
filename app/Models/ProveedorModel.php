<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Proveedor;

class ProveedorModel extends Model
{
    protected $table = 'proveedor';//Nuestra tabla en la bd
    protected $primaryKey = 'id_proveedor';
    protected $allowedFields = ['nombre_proveedor'];
    protected $useTimestamps = false; //Para evitar guardar y asignar fechas automaticamente
    
    // Funcion que crea un objeto de la entidad Proveedor.
    private function crearObjeto(array $registro): Proveedor
    {
        return new Proveedor(
            (int) $registro['id_proveedor'],
            $registro['nombre_proveedor'],
        );
    }

    /* Funcion que obtiene todos los registros de la BD que son de la clase proveedor
    y para cada uno de estos registros va creando un objeto de la entidad Proveedor*/
    public function obtenerTodos(): array
    {
        $registros = $this->orderBy('nombre_proveedor', 'ASC')->findAll();

        return array_map(fn($r) => $this->crearObjeto($r), $registros);
    }

}