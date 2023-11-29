<?php
declare(strict_types = 1);
const ROOT_DIR = __DIR__;
require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/app/routes.php';

use Kikopolis\CustomerCrud\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

global $routes;
$dotenv = new Dotenv();
$dotenv->loadEnv(ROOT_DIR . '/.env');

$request = Request::createFromGlobals();
$context = new RequestContext();
$matcher = new UrlMatcher($routes, $context);
$dispatcher = new EventDispatcher();
$controllerResolver = new ControllerResolver();
$argsResolver = new ArgumentResolver();
$kernel = new Kernel($matcher, $controllerResolver, $argsResolver);
$response = $kernel->handle($request);
$response->send();
