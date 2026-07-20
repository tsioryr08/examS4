<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Opérateur - préfixes
$routes->get('/operateurs', 'OperateurController::index');
$routes->get('/operateurs/create', 'OperateurController::create');
$routes->post('/operateurs/store', 'OperateurController::store');
$routes->get('/operateurs/edit/(:num)', 'OperateurController::edit/$1');
$routes->post('/operateurs/update/(:num)', 'OperateurController::update/$1');
$routes->get('/operateurs/delete/(:num)', 'OperateurController::delete/$1');

// Types d'opération
$routes->get('/types-operation', 'TypeOperationController::index');
$routes->get('/types-operation/create', 'TypeOperationController::create');
$routes->post('/types-operation/store', 'TypeOperationController::store');
$routes->get('/types-operation/edit/(:num)', 'TypeOperationController::edit/$1');
$routes->post('/types-operation/update/(:num)', 'TypeOperationController::update/$1');
$routes->get('/types-operation/delete/(:num)', 'TypeOperationController::delete/$1');

// Barème de frais
$routes->get('/baremes', 'BaremeFraisController::index');
$routes->get('/baremes/create', 'BaremeFraisController::create');
$routes->post('/baremes/store', 'BaremeFraisController::store');
$routes->get('/baremes/edit/(:num)', 'BaremeFraisController::edit/$1');
$routes->post('/baremes/update/(:num)', 'BaremeFraisController::update/$1');
$routes->get('/baremes/delete/(:num)', 'BaremeFraisController::delete/$1');

// Gains
$routes->get('/gains', 'GainController::index');

// Situation comptes clients
$routes->get('/comptes-clients', 'ClientCompteController::index');

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
// pour le transfert multiple
$routes->get('transfert/multiple', 'ClientController::transfertMultiple');
$routes->post('transfert/multiple/valider', 'ClientController::transfertMultipleValider');