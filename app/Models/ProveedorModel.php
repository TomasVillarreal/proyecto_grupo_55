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
    
    private ?array $cacheProveedores = null;

    public function obtenerTodos(): array
    {
        if ($this->cacheProveedores !== null) {
            return $this->cacheProveedores;
        }

        $registros = $this->orderBy('nombre_proveedor', 'ASC')->findAll();

        $tipos = [];

        foreach ($registros as $registro) {
            $tipos[] = new Proveedor(
                $registro['id_proveedor'],
                $registro['nombre_proveedor']
            );
        }

        $this->cacheProveedores = $tipos;

        return $this->cacheProveedores;
    }

}