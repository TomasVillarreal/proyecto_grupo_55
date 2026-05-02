<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//GET
$routes->get('/', 'MedicamentosController::vista_alta_medicamentos');
$routes->get('/modificacion_medicamento', 'MedicamentosController::vista_modificacion_medicamento');
$routes->get('/eliminacion_medicamento', 'MedicamentosController::vista_baja_medicamento');
$routes->get(
    'medicamentos/productos/(:num)',
    'MedicamentosController::productosPorMedicamento/$1'
);

//POST
$routes->post('medicamentos/alta', 'MedicamentosController::altaMedicamento');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','MedicamentosController::modificacionMedicamento');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.
//Rutas para las eliminaciones
$routes->post('productos/eliminacion/(:num)', 'ProductoFarmaceuticoController::bajaProducto/$1');
$routes->post('medicamentos/eliminacion/(:num)', 'MedicamentosController::bajaMedicamento/$1');