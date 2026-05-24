<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//MEDICAMENTOS

// -- GET -- 
$routes->get('/', 'MedicamentosController::mostrarAltaMedicamentos');
$routes->get('/update', 'MedicamentosController::mostrarModificacionMedicamentos');
$routes->get('/delete', 'MedicamentosController::mostrarBajaMedicamentos');
$routes->get(
    'medicamentos/productos/(:num)',
    'MedicamentosController::obtenerProductosPorMedicamento/$1'
);

// -- POST --
$routes->post('medicamentos/alta', 'MedicamentosController::crearMedicamento');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','MedicamentosController::modificarMedicamento');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
$routes->post('productos/delete/(:num)', 'ProductoFarmaceuticoController::eliminarProducto/$1');
$routes->post('medicamentos/delete/(:num)', 'MedicamentosController::eliminarMedicamento/$1');


// PEDIDOS

// -- GET --
$routes->get('/listaPedidos', 'PedidoController::mostrarListaPedidos');
$routes->get('/filtrarPedidos', 'PedidoController::mostrarListaFiltrada');
$routes->get('/crearPedido', 'PedidoController::mostrarCreacionPedidos');
$routes->get('/pedido/(:num)', 'PedidoController::mostrarDetallesPedidos/$1');

//POST
$routes->post('pedidos/aprobar', 'PedidoController::manejarAceptacion');
$routes->post('pedidos/rechazar', 'PedidoController::manejarRechazo');
$routes->post('pedidos/crearPedido', 'PedidoController::guardarDatosPedido');
