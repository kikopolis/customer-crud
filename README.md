# Required to run
* PHP 8.2 or higher
* Mysql 8.0 or higher
* Composer 2.6.5 or higher
* npm 10.1.0 or higher

# Installation
1. Clone the repository
2. Navigate to the project folder
3. Make sure mysql is running and the connection is configured in the .env file
4. Use the contents of the `create-db.sql` file to create the database and app user
5. Run `composer install`
6. Run `npm install`
7. Run `npm run dev`
8. Run `php -S localhost:9000` 
9. Open `localhost:9000/init` in your browser to initialize the db table and seed with some data
10. Open `localhost:5173` in your browser to view the Vue app
