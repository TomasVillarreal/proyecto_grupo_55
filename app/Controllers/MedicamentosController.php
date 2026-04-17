<?php

namespace App\Controllers;

class MedicamentosController extends BaseController
{
    public function create(): string
    {
            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create')
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
