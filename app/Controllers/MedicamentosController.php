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

        $medicamentos = $medicamentoModel->obtenerMedicamentosActivos();
        $tiposProductos = $tipoProductoModel->obtenerParaDropdown();
        $unidadesMedida = $medidaModel->obtenerParaDropdown();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadesMedida, 'tiposProducto'=>$tiposProductos])
        ]);
    }

    public function productosPorMedicamento($idMedicamento)
    {
        $productoModel = new \App\Models\ProductoFarmaceuticoModel();

        $productos = $productoModel->obtenerProductosPorMedicamento((int)$idMedicamento);

        return $this->response->setJSON($productos);
    }

    public function update(): string
    {
        $medicamentoModel = new MedicamentoModel();
        $tipoProductoModel = new TipoProductoModel();
        $medidaModel = new MedidaProductoModel();

        $tiposProductos = $tipoProductoModel->obtenerParaDropdown();
        $unidadesMedida = $medidaModel->obtenerParaDropdown();
        $medicamentos = $medicamentoModel->obtenerMedicamentosActivos();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/update', ['medicamentos'=>$medicamentos, 'unidadesMedida'=>$unidadesMedida, 'tiposProducto'=>$tiposProductos])
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
