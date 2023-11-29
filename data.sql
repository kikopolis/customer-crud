create database if not exists 'customercrud';
use 'customercrud';
create user 'app'@'localhost' identified by 'password';
grant all on 'customercrud'.* to 'app'@'localhost';
