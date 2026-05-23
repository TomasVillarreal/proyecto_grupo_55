<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//MEDICAMENTOS

// -- GET -- 
$routes->get('/', 'MedicamentosController::vista_alta_medicamentos');
$routes->get('/update', 'MedicamentosController::vista_modificacion_medicamento');
$routes->get('/delete', 'MedicamentosController::vista_baja_medicamento');
$routes->get(
    'medicamentos/productos/(:num)',
    'MedicamentosController::productosPorMedicamento/$1'
);

// -- POST --
$routes->post('medicamentos/alta', 'MedicamentosController::altaMedicamento');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','MedicamentosController::modificacionMedicamento');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
$routes->post('productos/delete/(:num)', 'ProductoFarmaceuticoController::bajaProducto/$1');
$routes->post('medicamentos/delete/(:num)', 'MedicamentosController::bajaMedicamento/$1');


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
