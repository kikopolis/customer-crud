<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Model;

use Kikopolis\CustomerCrud\Database\Model;

class Customer extends Model {
    protected string $table = 'customers';
    protected array $fillable = [
        'first_name',
        'last_name',
        'username',
        'password',
        'date_of_birth',
    ];
    protected array $hidden = [
        'password',
    ];
    protected array $rules = [
        'first_name'    => 'required|string',
        'last_name'     => 'required|string',
        'username'      => 'required|string|unique',
        'password'      => 'required|string',
        'date_of_birth' => 'required|date',
    ];
}
