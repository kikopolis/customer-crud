<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

readonly class Kernel implements HttpKernelInterface {
    public function __construct(
        private UrlMatcher         $matcher,
        private ControllerResolver $controllerResolver,
        private ArgumentResolver   $argumentResolver,
    ) {}
    
    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response {
        $this->matcher->getContext()->fromRequest($request);
        try {
            $pathInfo = $request->getPathInfo();
            $attributes = $this->matcher->match($pathInfo);
            $request->attributes->add($attributes);
            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);
            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException) {
            return new Response('Not Found', 404);
        } catch (Exception $e) {
            return new Response('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
