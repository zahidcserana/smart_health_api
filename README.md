# Lumen PHP Framework

# tymon/jwt-auth
## command
composer require tymon/jwt-auth

# change bootstrap/app.php

// Uncomment this line
$app->register(App\Providers\AuthServiceProvider::class);

// Add this line
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

Then uncomment the auth middleware in the same file:

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

#Generate secret key
 php artisan jwt:secret

1. config/auth.php
2. bootstrap/app.php
3. User.php
4. AuthController.php
5. .env
6. web.php
# tymon-jwt-auth
# smart_health_api
