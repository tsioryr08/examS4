<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'ClientController::login');
$routes->get('login', 'ClientController::login');
$routes->post('login/verifier', 'ClientController::verifier');
$routes->get('logout', 'ClientController::logout');
$routes->get('dashboard', 'ClientController::dashboard');

// pour le depot
$routes->get('depot', 'ClientController::depot');
$routes->post('depot/valider', 'ClientController::depotValider');
// pour le retrait
$routes->get('retrait', 'ClientController::retrait');
$routes->post('retrait/valider', 'ClientController::retraitValider');
// pour le transfert
$routes->get('transfert', 'ClientController::transfert');
$routes->post('transfert/valider', 'ClientController::transfertValider');