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

    /*Metodo que carga los datos a la vista de la lista de pedidos*/
    public function listaPedidos(): string
    {
        $pedidos = $this->pedidoService->obtenerPedidos();
        $estados = $this->estadoService->obtenerEstadosDropdown();
        $servicios = $this->servicioService->obtenerServiciosDropdown();

        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/lista', ['pedidos'=>$pedidos, 'estados'=>$estados, 'servicios'=>$servicios])
        ]);
    }
    
    /*Metodo para obtener los pedidos filtrados segun el estado y el servicio medico.
     * Retorna JSON.
     * Es usada en la vista de la lista de pedidos.
     */
    public function filtrarPedidos(int $idEstado, int $idServicio)
    {
        $pedidosFiltrados = $this->pedidoService->realizarFiltradoPedidos($idEstado, $idServicio);

        return $this->response->setJSON($pedidosFiltrados);
    }
}