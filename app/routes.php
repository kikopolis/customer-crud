<?php
declare(strict_types = 1);

use Kikopolis\CustomerCrud\Controller\CustomerController;
use Kikopolis\CustomerCrud\Controller\HomeController;
use Kikopolis\CustomerCrud\Controller\InitializeController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('home', new Route('/', ['_controller' => [HomeController::class, 'index'], '_method' => 'GET']));
$routes->add('init', new Route('/init', ['_controller' => [InitializeController::class, 'index'], '_method' => 'GET']));
$routes->add('customers', new Route('/customers', ['_controller' => [CustomerController::class, 'all'], '_method' => 'GET']));
$routes->add('create', new Route('/customers/create', ['_controller' => [CustomerController::class, 'create'], '_method' => 'POST']));
$routes->add('update', new Route('/customers/update/{id}', ['_controller' => [CustomerController::class, 'update'], '_method' => 'POST']));
$routes->add('delete', new Route('/customers/delete/{id}', ['_controller' => [CustomerController::class, 'delete'], '_method' => 'DELETE']));
