<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//GET
$routes->get('/', 'MedicamentosController::create');
$routes->get('/update', 'MedicamentosController::update');
$routes->get('/delete', 'MedicamentosController::delete');
$routes->get(
    'medicamentos/productos/(:num)',
    'MedicamentosController::productosPorMedicamento/$1'
);
$routes->get('/listaPedidos', 'PedidoController::mostrarListaPedidos');
$routes->get('/crearPedido', 'PedidoController::mostrarCreacionPedidos');
$routes->get('/pedido/(:num)', 'PedidoController::mostrarDetallesPedidos/$1');

//POST
$routes->post('medicamentos/alta', 'MedicamentosController::altaMedicamento');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','MedicamentosController::modificacionMedicamento');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
$routes->post('pedidos/aprobar', 'PedidoController::aprobarPedido');
$routes->post('pedidos/rechazar', 'PedidoController::rechazarPedido');
$routes->post('pedidos/crearPedido', 'PedidoController::guardarDatosPedido');
//Rutas para las eliminaciones
$routes->post('productos/delete/(:num)', 'ProductoFarmaceuticoController::bajaProducto/$1');
$routes->post('medicamentos/delete/(:num)', 'MedicamentosController::bajaMedicamento/$1');