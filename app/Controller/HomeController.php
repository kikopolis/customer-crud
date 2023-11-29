<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController {
    public function index(): Response {
        return new Response('Hello World! This is only an API. No web interface is available.');
    }
}
