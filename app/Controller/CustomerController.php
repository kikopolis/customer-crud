<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Controller;

use Kikopolis\CustomerCrud\Model\Customer;
use Symfony\Component\HttpFoundation\Response;

class CustomerController {
    public function all(): Response {
        $customers = Customer::all();
        $response = new Response();
        $response->setContent(json_encode($customers));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function create(): Response {
        $customer = new Customer(
            [
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'username'      => 'johndoe1112',
                'password'      => 'password',
                'date_of_birth' => '1990-01-01',
            ]
        );
        $customer->save();
        $response = new Response();
        $response->setContent(json_encode($customer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function update(): Response {
        $customer = Customer::find(1);
        $customer->first_name = 'Archie';
        $customer->last_name = '22';
        $customer->username = 'arcrow';
        $customer->password = 'arcrow';
        $customer->date_of_birth = '1300-01-01';
        $customer->sync();
        $response = new Response();
        $response->setContent(json_encode($customer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function delete(): Response {
        Customer::delete(1);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
