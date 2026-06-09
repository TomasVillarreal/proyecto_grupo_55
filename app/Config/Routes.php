<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//MEDICAMENTOS

// -- GET -- Con filtro de acceso solo para loggeados
$routes->get('/', 'ProductoFarmaceuticoController::mostrarAltaProductos', ['filter' => 'auth']);

$routes->get('/modificarMed', 'ProductoFarmaceuticoController::mostrarModificacionProductos', ['filter' => 'auth']);

$routes->get('/eliminarMed', 'ProductoFarmaceuticoController::mostrarBajaProductos', ['filter' => 'auth']);

$routes->get(
    'medicamentos/productos/(:num)',
    'ProductoFarmaceuticoController::obtenerProductosPorMedicamento/$1'
);

// -- POST -- Con filtro de acceso solo para loggeados
$routes->post('medicamentos/alta', 'ProductoFarmaceuticoController::manejarCreacionProducto', ['filter' => 'auth']);
//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','ProductoFarmaceuticoController::manejarModificacionProducto', ['filter' => 'auth']);
//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
$routes->post('productos/eliminar/(:num)', 'ProductoFarmaceuticoController::manejarEliminacionProducto/$1', ['filter' => 'auth']);
$routes->post('medicamentos/eliminar/(:num)', 'MedicamentosController::manejarEliminacionMedicamento/$1', ['filter' => 'auth']);



// PEDIDOS

// -- GET -- Con filtro de acceso solo para loggeados y responsables
$routes->get('/listaPedidos', 'PedidoController::mostrarListaPedidos', ['filter' => 'auth']);
$routes->get('/filtrarPedidos', 'PedidoController::mostrarListaFiltrada', ['filter' => 'auth']);
$routes->get('/crearPedido', 'PedidoController::mostrarCreacionPedidos', ['filter' => 'auth']);
$routes->get('/pedido/(:num)', 'PedidoController::mostrarDetallesPedidos/$1', ['filter' => 'auth']);

//POST
$routes->post('pedidos/aprobar', 'PedidoController::manejarAceptacion', ['filter' => ['auth', 'responsable']]);
$routes->post('pedidos/rechazar', 'PedidoController::manejarRechazo', ['filter' => ['auth', 'responsable']]);
$routes->post('pedidos/crearPedido', 'PedidoController::guardarDatosPedido', ['filter' => 'auth']);

// LOGIN/INICIO DE SESION

// -- GET -- Con filtro de acceso solo para no loggeados
$routes->get('/access/login', 'LoginController::mostrarLogin');

// -- POST -- Con filtro de acceso solo para no loggeados
$routes->post('/access/iniciar_sesion', 'LoginController::login');


// USUARIO

// -- GET -- Con filtro de acceso solo para loggeados y responsables
$routes->get('/access/registrar', 'UsuarioController::mostrarAltaUsuario', ['filter' => ['auth', 'responsable']]);
$routes->get('/access/logout', 'LoginController::logout', ['filter' => 'auth']);

// -- POST -- Con filtro de acceso solo para loggeados y responsables
$routes->post('/access/crear', 'UsuarioController::manejarCreacionUsuario', ['filter' => ['auth', 'responsable']]);