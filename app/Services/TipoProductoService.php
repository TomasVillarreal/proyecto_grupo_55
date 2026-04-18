<?php

namespace App\Services;

use App\Models\TipoProductoModel;

class TipoProductoService
{
    //Variable a utilizar que hace referncia al modelo
    protected $tipoProductoModel;

    /*Creacion del constructor para evitar llamar al modelo en cada funcion*/
    public function __construct()
    {
        $this->tipoProductoModel = model(TipoProductoModel::class);//Se reconoce e instancia la clase
    }

    /*Metodo para obtener los tipos de productos y ser utilizados en el dropdown*/
    public function obtenerParaDropdown(): array
    {
        $tipos = $this->tipoProductoModel->orderBy('nombre_tipo_producto', 'ASC')->findAll();
        $listado = [];
        foreach ($tipos as $tipo) {
            $listado[$tipo->id_tipo_producto] = $tipo->nombre_tipo_producto;
        }
        return $listado;
    }
}