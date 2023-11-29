<?php
declare(strict_types = 1);

use Kikopolis\CustomerCrud\Database\DB;

return new class {
    public function __invoke(): void {
        $connection = DB::getInstance()->getConnection();
        $sql = <<<SQL
drop table if exists customers;
create table customers (
    id int unsigned auto_increment primary key,
    first_name varchar(255) not null,
    last_name varchar(255) not null,
    username varchar(255) not null,
    password text not null,
    date_of_birth date not null,
    created_at timestamp default current_timestamp,
    updated_at timestamp default null on update current_timestamp
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;
SQL;
        $stmt = $connection->prepare($sql);
        $stmt->execute();
    }
};
