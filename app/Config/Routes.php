<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//MEDICAMENTOS

// -- GET -- 
$routes->get('/', 'ProductoFarmaceuticoController::mostrarAltaProductos');
$routes->get('/modificarMed', 'ProductoFarmaceuticoController::mostrarModificacionProductos');
$routes->get('/eliminarMed', 'ProductoFarmaceuticoController::mostrarBajaProductos');
$routes->get(
    'medicamentos/productos/(:num)',
    'ProductoFarmaceuticoController::obtenerProductosPorMedicamento/$1'
);

// -- POST --
$routes->post('medicamentos/alta', 'ProductoFarmaceuticoController::crearProducto');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','ProductoFarmaceuticoController::modificarProducto');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
$routes->post('productos/eliminar/(:num)', 'ProductoFarmaceuticoController::eliminarProducto/$1');
$routes->post('medicamentos/eliminar/(:num)', 'MedicamentosController::eliminarMedicamento/$1');


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
