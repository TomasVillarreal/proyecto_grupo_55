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
}