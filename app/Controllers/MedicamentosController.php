<?php

namespace App\Controllers;

use App\Models\MedicamentoModel;

class MedicamentosController extends BaseController
{
    public function create(): string
    {
        $medicamentoModel = new MedicamentoModel();
        $medicamentos = $medicamentoModel->findAll();

            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create', ['medicamentos'=>$medicamentos])
        ]);
    }

    public function update(): string
    {
            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/update')
        ]);
    }

    public function delete(): string
    {
            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/delete')
        ]);
    }
}
