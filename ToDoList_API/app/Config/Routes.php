<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// === Rotas para manipulação de tarefas ===
$routes->group('task', static function ($routes) {   
    $routes->get('/', 'Task::index', ['as' => 'task']);
    $routes->post('store', 'Task::store', ['as' => 'task.store']);
    $routes->get('edit/(:num)', 'Task::edit/$1/', ['as' => 'task.edit']);
    $routes->put('update/(:num)', 'Task::update/$1', ['as' => 'task.update']);
    $routes->delete('delete/(:num)', 'Task::delete/$1', ['as' => 'task.delete']);
    $routes->patch('status/(:any)', 'Task::status/$1', ['as' => 'task.status']);
});