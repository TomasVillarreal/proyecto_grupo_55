<?php

namespace App\Controllers;

use App\Services\CatalogoService;
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
    protected PedidoService $pedidoService;
    protected EstadoPedidoService $estadoService;
    protected CatalogoService $servicioService;
    protected DetallePedidoService $detalleService;
    protected ProveedorService $proveedorService;
    protected MedicamentoService $medicamentoService;
    protected ProductoFarmaceuticoService $productoService;

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

    // Metodo que obtiene todos los pedidos filtrados
    private function obtenerPedidosFiltrados() : array
    {
        /* agarro los datos de los filtros que vienen en la query string (si es que viene por ajax)
         en caso contrario les coloco un 0 (el 0 actua como el valor default).
         agarro tambien el orden de la tabla segun la fecha*/
        $idEstado = (int) ($this->request->getGet('idEstado') ?? 0);
        $idServicio = (int) ($this->request->getGet('idServicio') ?? 0);
        $orden = strtoupper($this->request->getGet('orden') ?? 'ASC');

        // validacion para que el orden solo pueda ser ASC o DESC, y no cualquier otra cosa
        $orden = in_array($orden, ['ASC', 'DESC']) ? $orden : 'ASC';

        // agarro los pedidos
        $pedidos = $this->pedidoService->obtenerPedidos($idEstado, $idServicio, $orden);

        // devuelvo los pedidos
        return ['pedidos' => $pedidos];
    }

    // Metodo que obtiene todos los datos auxiliares a utilizar en las funciones.
    private function obtenerDatosAuxiliares(): array
    {
        return [
            'estados' => $this->estadoService->obtenerOpcionesDropdown(),
            'servicios' => $this->servicioService->obtenerOpcionesDropdown(),
            'proveedores' => $this->proveedorService->obtenerOpcionesDropdown(),
            'medicamentos' => $this->medicamentoService->obtenerMedicamentosDropdown(),
        ];
    }

    //Metodo que carga la vista de la lista de pedidos
    public function mostrarListaPedidos(): string
    {
        $data = array_merge($this->obtenerDatosAuxiliares(), $this->obtenerPedidosFiltrados());
        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/lista', $data)
        ]);
    }

    // Metodo que se llamara para mostrar la lista de pedidos filtrada
    public function mostrarListaFiltrada(): string
    {
        $data = $this->obtenerPedidosFiltrados();
        return view('pedidos/_tabla', $data);
    }

    // Metodo que carga la vista para ver los detalles de un pedido
    public function mostrarDetallesPedidos(int $idPedido) : string
    {
        $data['pedido'] = $this->pedidoService->obtenerPedidoEspecifico($idPedido);
        $data['detalles_pedido'] = $this->detalleService->obtenerDetallesPedido($data['pedido']['id_pedido']);

        return view('layout/main_layout', [
            'title' => 'Lista de Pedidos - Clinicks',
            'content' => view('pedidos/detallePedido', $data)
        ]);
    }

    /* Metodo descompuesto que abstrae la logica del try catch que tenian previamente
    las funciones de aceptar y rechazar pedidos.
    Usa un parametro Callable que permitira la invocacion de la funcion pasada como argumento*/
    private function ejecutarAccionPedido(callable $accion)
    {
        try {
            // Llama a la accion del argumento
            $accion();
            return redirect()->back();
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error inesperado.');
        }
    }

    // Metodo que maneja la aceptacion de un pedido
    public function manejarAceptacion()
    {
        $id = (int) $this->request->getPost('idPedido');
        return $this->ejecutarAccionPedido(fn() => $this->pedidoService->aprobarPedido((int)$id));
    }

    // Metodo que maneja el rechazo de un pedido
    public function manejarRechazo()
    {
        $id = (int) $this->request->getPost('idPedido');
        $motivo = trim($this->request->getPost('motivo_rechazo')) ?: '-';
        return $this->ejecutarAccionPedido(fn() => $this->pedidoService->rechazarPedido((int)$id, $motivo));
    }

    // Metodo que muestra la vista de creacion de pedidos
    public function mostrarCreacionPedidos() : string
    {
        $data = $this->obtenerDatosAuxiliares();
        return view('layout/main_layout', [
            'title' => 'Crear pedido - Clinicks',
            'content' => view('pedidos/crearPedido', $data)
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