<?php

namespace App\Controllers;

class MedicamentosController extends BaseController
{
    public function index(): string
    {
            return view('layout/main_layout', [
            'title' => 'Medicamentos - Clinicks',
            'content' => view('medicamentos/create')
        ]);
    }
}
