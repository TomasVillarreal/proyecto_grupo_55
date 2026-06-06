<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ResponsableFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        //Filtro para el rol de los responsables, aca no se ve mucho, si en las rutas
        if (session()->get('nombre_rol') !== 'Responsable') {
            return redirect()->to('/');
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {}
}