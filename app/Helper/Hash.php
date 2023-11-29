<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Helper;

class Hash {
    public static function make(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public static function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}
