# Goodday LGP

## Setup (locally)

#### 1. Initialise Laravel

The platform is powered by Laravel framework, so you need to follow 
the standard setup process of Laravel to get started:

```bash
$ npm install
$ composer install
$ cp .env.example .env
$ php artisan key:generate # generate app encryption key
$ php artisan storage:link # create storage symlink for public file uploads
```

#### 2. Specify DB credentials in .env and populate database

Fill .env file with your database settings.

Next you need to run standard procedures like filling database and creating default admin user.
You can try this prebuilt command for local environments that will take care of this for you:

```bash
$ php artisan goodday:initial-setup
```

If it all worked well, feel free to proceed to the next chapter *Usage (locally)*.


#### Alternative, manual setup
Alternatively, you can complete the setup manually in multiple steps, start with running DB migrations:

```bash
$ php artisan migrate
```

Register a new admin user using one of methods:
1) Create admin user manually
2) OR Register using frontend (you need working frontend first and `REGISTRATION_ENABLED` enabled)
3) OR run `php artisan db:seed --class=UserSeeder` 

When you have the user, assign all permissions to him by running the below artisan command:

```bash
$ php artisan permission:give
```

You then can access admin area using the "/{locale}/admincp" route.

## Usage (locally)

The recommended way for running your Laravel app since 2022 is using Laravel Sail. 
You need to have UNIX terminal to run that - with that you can run the below to start your app.
Otherwise, you should be able to utilise the built-in PHP server instead:

```bash
$ sail up -d
$ # php artisan serve # use it only if Sail is not available
```

To compile assets, run:

```bash
$ npm run dev
```

## Setup (in production)

Assets should be pushed to production environment every time you change them
(this means pushing files in public/build folder to production)

If the production server is using FastCGI to substitute PHP version, you have to prepend all 
PHP-binary calls with its full path, like this is how you would call composer install on server with PHP81 in /opt/:

```bash
$  /opt/php81/bin/php /usr/local/bin/composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
```

You will also need to run this *when firstly installing the app* on production server:

```bash
$ cp .env.example .env
$ # fill DB settings
$ /opt/php81/bin/php artisan key:generate
$ /opt/php81/bin/php artisan storage:link
$ chown -R www-root:www-root .
$ find storage/* -type f -exec chmod 644 {} \;   
$ find storage/* -type d -exec chmod 755 {} \;
$ /opt/php81/bin/php artisan migrate
```

## Usage (in production)

After production environment is set up, you usually do not need to worry about anything other than
getting your code updates to production environment that is achieved via simple `git pull`. 
If a change requires composer install or DB migrations - run those manually on production server, 
there is not any automation set up for it.
