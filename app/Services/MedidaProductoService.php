<?php

namespace App\Services;

use App\Models\MedidaProductoModel;

class MedidaProductoService
{
    //Variable a utilizar que hace referncia al modelo
    protected $medidaProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->medidaProductoModel = model(MedidaProductoModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener las medidas de los productos y luego utilizarlos en el dropdown*/
    public function obtenerMedidaDropdown(): array
    {
        $medidas = $this->medidaProductoModel->orderBy('nombre_medida', 'ASC')->findAll();
        $listado = [];
        foreach ($medidas as $medida) {
            $listado[$medida->id_medida_producto] = $medida->nombre_medida;
        }
        return $listado;
    }
}