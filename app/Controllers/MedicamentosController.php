<?php

namespace App\Controllers;

use App\Models\MedicamentoModel;
use App\Models\MedidaProductoModel;
use App\Models\ProductoFarmaceuticoModel;
use App\Models\TipoProductoModel;

class MedicamentosController extends BaseController
{
    public function create(): string
    {
        $medicamentoModel = new MedicamentoModel();
        $tipoProductoModel = new TipoProductoModel();
        $medidaModel = new MedidaProductoModel();

        $medicamentos = $medicamentoModel->findAll();
        $tiposProductos = $tipoProductoModel->findAll();
        $unidadesMedida = $medidaModel->findAll();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadesMedida, 'tiposProducto'=>$tiposProductos])
        ]);
    }

    public function update(): string
    {
        $medicamentoModel = new MedicamentoModel();
        $productoModel = new ProductoFarmaceuticoModel();
        $tipoProductoModel = new TipoProductoModel();
        $medidaModel = new MedidaProductoModel();

        $tiposProductos = $tipoProductoModel->findAll();
        $unidadesMedida = $medidaModel->findAll();
        $medicamentos = $medicamentoModel->findAll();
        $productos = $productoModel->findAll();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/update', ['medicamentos'=>$medicamentos, 'productos'=>$productos, 'unidadesMedida'=>$unidadesMedida, 'tiposProducto'=>$tiposProductos])
        ]);
    }

    public function delete(): string
    {
        $medicamentoModel = new MedicamentoModel();

        $medicamentos = $medicamentoModel->findAll();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/delete', ['medicamentos'=>$medicamentos])
        ]);
    }
}
