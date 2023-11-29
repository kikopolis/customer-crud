<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Controller;

use Symfony\Component\HttpFoundation\Response;

class InitializeController {
    public function index(): Response {
        $create_customers_table = require_once ROOT_DIR . '/app/Database/migrations/create_customers_table.php';
        $create_customers_table();
        $seed_customers_table = require_once ROOT_DIR . '/app/Database/seeds/customer_seed.php';
        $seed_customers_table();
        return new Response('Database initialized');
    }
}
