<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'MedicamentosController::create');
$routes->get('/update', 'MedicamentosController::update');
$routes->get('/delete', 'MedicamentosController::delete');
