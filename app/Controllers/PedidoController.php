<?php

namespace App\Controllers;

use App\Services\PedidoService;

class PedidoController extends BaseController
{
    //Se crea la variable a utilizar del servicio de los pedidos
    protected $pedidoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->pedidoService = new PedidoService();
    }

    /*Metodo que carga los datos a la vista de la lista de pedidos*/
    public function listaPedidos(): string
    {
        $pedidos = $this->pedidoService->obtenerPedidos();

        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/lista', ['pedidos'=>$pedidos])
        ]);
    }
    
}