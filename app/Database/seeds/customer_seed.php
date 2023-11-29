<?php
declare(strict_types = 1);

use Kikopolis\CustomerCrud\Database\DB;

return new class {
    public function __invoke(): void {
        $password = password_hash('password', PASSWORD_DEFAULT);
        $connection = DB::getInstance()->getConnection();
        $sql = <<<SQL
insert into customers (first_name, last_name, username, password, date_of_birth) values
    ('John', 'Doe', 'johndoe', '$password', '1990-01-01'),
    ('Jane', 'Doe', 'janedoe', '$password', '1990-01-01'),
    ('John', 'Smith', 'johnsmith', '$password', '1990-01-01'),
    ('Jane', 'Smith', 'janesmith', '$password', '1990-01-01'),
    ('John', 'Jones', 'johnjones', '$password', '1990-01-01'),
    ('Jane', 'Jones', 'janejones', '$password', '1990-01-01');
SQL;
        $stmt = $connection->prepare($sql);
        $stmt->execute();
    }
};
