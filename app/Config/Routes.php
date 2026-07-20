<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'ClientController::login');
$routes->get('login', 'ClientController::login');
$routes->post('login/verifier', 'ClientController::verifier');
$routes->get('logout', 'ClientController::logout');