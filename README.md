
## Newswire Developer Testing Project
## Name: Misael Sauceda
## Email: mcsdev.contato@gmail.com

In my submission for this test I've used the sample boilerplate code in InertiaJS and implemented the requirements while trying to maintain a code style similar to the one in the boilerplate. I would be happy to explain the choices I've made and discuss further.

The only additional step to the local deployment proccess that is needed is to set the APP_ENV to 'production' if you want to see the error information supressed by the error handler requested.

Additionaly, I've added 2 buttons for creating a new task to make crud operations easier to test. (Providing 2 or more implementation options for the business team when an unexpected requirement is found during development is a good practice that I aim to follow)

### Using this project

Requirements: `PHP/composer` and `Docker`

-  Save the file `.env.example` as `.env` (change the APP_ENV to production)
-  Run `composer install` To install dependencies
-  Bring the laravel sail containers up with `./vendor/bin/sail up -d`
-  Run `./vendor/bin/sail npm install` To use the npm from the default docker provided by Laravel Sail 
-  Run `./vendor/bin/sail artisan migrate` To initialize the sqlite database (it should create database/database.sqlite file)
-  Run `./vendor/bin/sail npm run build` To build and to watch for changes replace `build` with `dev`
-  Go to http://localhost:8080 and register an account
