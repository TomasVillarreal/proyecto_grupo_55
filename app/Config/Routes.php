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

//POST
$routes->post('medicamentos/alta', 'MedicamentosController::altaMedicamento');//Ruta con el POST para la cracion de medicamentos y/o productos farm.
$routes->post('medicamentos/modificacion','MedicamentosController::modificacionMedicamento');//Ruta con el POST para la modificacion de medicamentos y/o productos farm.