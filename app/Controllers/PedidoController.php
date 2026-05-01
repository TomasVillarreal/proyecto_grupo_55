<?php

namespace App\Controllers;

use App\Services\PedidoService;
use App\Services\EstadoPedidoService;
use App\Services\ServicioMedicoService;

class PedidoController extends BaseController
{
    //Se crea la variable a utilizar del servicio de los pedidos
    protected $pedidoService;
    protected $estadoService;
    protected $servicioService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->pedidoService = new PedidoService();
        $this->estadoService = new EstadoPedidoService();
        $this->servicioService = new ServicioMedicoService();
    }

    /*Metodo que carga los datos a la vista de la lista de pedidos, y para el filtrado en caso de que se desee*/
    public function listaPedidos(): string
    {
        // agarro los datos de los filtros que vienen en la query string (si es que viene por ajax)
        // en caso contrario les coloco un 0 (el 0 actua como el valor default)
        $idEstado = $this->request->getGet('idEstado') ?? 0;
        $idServicio = $this->request->getGet('idServicio') ?? 0;

        // cargo los pedidos a mandar
        $pedidos = $this->pedidoService->obtenerPedidos((int)$idEstado, (int)$idServicio);

        // aca me pregunto si la request viene del navegador o del ajax
        if ($this->request->isAJAX()) {
            // si la request viene del ajax (lo sabemos por el header), solo mando la tabla actualizada 
            return view('pedidos/_tabla', ['pedidos' => $pedidos]);
        }

        $estados = $this->estadoService->obtenerEstadosDropdown();
        $servicios = $this->servicioService->obtenerServiciosDropdown();

        // si vino por aca, la request viene del navegador y cargo toda la pagina
        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/lista', [
                'pedidos' => $pedidos,
                'estados' => $estados,
                'servicios' => $servicios
            ])
        ]);
    }
}