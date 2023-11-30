<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Controller;

use Kikopolis\CustomerCrud\Model\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController {
    public function all(): Response {
        $customers = Customer::all();
        $response = new Response();
        $response->setContent(json_encode($customers));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function create(Request $request): Response {
        $content = json_decode($request->getContent(), true);
        $customer = new Customer($content);
        $customer->save();
        $response = new Response();
        $response->setContent(json_encode($customer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function update(int $id, Request $request): Response {
        $customer = Customer::find($id);
        $content = json_decode($request->getContent(), true);
        $customer->sync($content);
        $response = new Response();
        $response->setContent(json_encode($customer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function delete(int $id): Response {
        Customer::delete($id);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
