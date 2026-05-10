<?php

namespace App\Controllers;

use App\Services\DetallePedidoService;
use App\Services\PedidoService;
use App\Services\EstadoPedidoService;
use App\Services\ProductoFarmaceuticoService;
use App\Services\ServicioMedicoService;
use App\Services\ProveedorService;
use App\Services\MedicamentoService;

class PedidoController extends BaseController
{
    //Se crea la variable a utilizar del servicio de los pedidos
    protected $pedidoService;
    protected $estadoService;
    protected $servicioService;
    protected $detalleService;
    protected $proveedorService;
    protected $medicamentoService;
    protected $productoService;

    /*Creacion del constructor para evitar llamar al servicio en cada funcion*/
    public function __construct()
    {
        //Se instancian los servicios
        $this->pedidoService = new PedidoService();
        $this->estadoService = new EstadoPedidoService();
        $this->servicioService = new ServicioMedicoService();
        $this->detalleService = new DetallePedidoService();
        $this->proveedorService = new ProveedorService();
        $this->medicamentoService = new MedicamentoService();
        $this->productoService = new ProductoFarmaceuticoService();
    }

    /*Metodo que carga los datos a la vista de la lista de pedidos, y para el filtrado en caso de que se desee*/
    public function mostrarListaPedidos(): string
    {
        /* agarro los datos de los filtros que vienen en la query string (si es que viene por ajax)
         en caso contrario les coloco un 0 (el 0 actua como el valor default).
         agarro tambien el orden de la tabla segun la fecha*/

        $idEstado = $this->request->getGet('idEstado') ?? 0;
        $idServicio = $this->request->getGet('idServicio') ?? 0;
        $orden = $this->request->getGet('orden') ?? 'ASC';

        // validacion para que el orden solo pueda ser ASC o DESC, y no cualquier otra cosa
        $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';

        // cargo los pedidos a mandar
        $pedidos = $this->pedidoService->obtenerPedidos((int)$idEstado, (int)$idServicio, $orden);

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

    public function mostrarDetallesPedidos(int $idPedido) : string
    {
        $pedido = $this->pedidoService->obtenerPedidoEspecifico($idPedido);
        $detalles_pedido = $this->detalleService->obtenerDetallesPedido($idPedido);

        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/detallePedido', [
                'pedido' => $pedido,
                'detalles' => $detalles_pedido,
            ])
        ]);
    }

    public function aprobarPedido()
    {
        try {
            $idPedido = $this->request->getPost('idPedido');
            $this->pedidoService->aprobarPedido((int)$idPedido);
            return redirect()->back();       
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error inesperado.');
        }
    }

    public function rechazarPedido()
    {
        try {
            $idPedido = $this->request->getPost('idPedido');
            $mensaje_rechazo = trim($this->request->getPost('motivo_rechazo')) ?: '-';
            $this->pedidoService->rechazarPedido((int)$idPedido, $mensaje_rechazo);
            return redirect()->back();       
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error inesperado.');
        }
    }

    public function mostrarCreacionPedidos() : string
    {
        $servicios = $this->servicioService->obtenerServiciosDropdown();
        $proveedores = $this->proveedorService->obtenerProveedoresDropdown();
        $medicamentos = $this->medicamentoService->obtenerMedicamentosDropdown();

        return view('layout/main_layout', [
            'title' => 'Crear pedido - Clinicks',
            'content' => view('pedidos/crearPedido', [
                'servicios' => $servicios,
                'proveedores' => $proveedores,
                'medicamentos'=> $medicamentos
            ])
        ]);
    }

    public function guardarDatosPedido()
    {
        // Datos generales
        $idServicio = $this->request->getPost('id_servicio_medico');
        $fecha = $this->request->getPost('fecha_solicitud_pedido');
        $comentario = $this->request->getPost('comentario_pedido');

        // Detalles
        $detalles = $this->request->getPost('detalles');

        dd($detalles);
    }
}