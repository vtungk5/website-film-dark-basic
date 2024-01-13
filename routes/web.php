<?php

use MiladRahimi\PhpRouter\Router;

$Router = Router::create();

$Router->get('/', [App\Controllers\PAGES::class, 'index']);

$Router->get('/{slug}/{IDPart}/{episode}.html', [App\Controllers\PAGES::class, 'video']);


$Router->group(['prefix' =>'/admin', 'middleware' => [Admin::class]], function (Router $Router) {
    $Router->get('/', [App\Controllers\Admin::class, 'index']);
    $Router->group(['prefix' => '/film'], function (Router $Router) {
        $Router->any('/', [App\Controllers\Admin::class, 'film']);
        $Router->any('/{IDFilm}/edit', [App\Controllers\Admin::class, 'Editfilm']);
        $Router->group(['prefix' => '/{IDFilm}/part'], function (Router $Router) {
            $Router->any('/', [App\Controllers\Admin::class, 'part']);
            $Router->group(['prefix' => '/{IDPart}'], function (Router $Router) {
                $Router->any('/edit', [App\Controllers\Admin::class, 'Editpart']);
                $Router->any('/video', [App\Controllers\Admin::class, 'video']);
                $Router->any('/video/{IDVideo}/edit', [App\Controllers\Admin::class, 'Editvideo']);
            });
        });
    });
});


$Router->group(
    ['prefix' => '/api'],
    function (Router $Router) {

        $Router->group(['prefix' => '/auth', 'middleware' => [AuthAPIMiddleware::class]], function (Router $Router) {

            $Router->post('/login', [App\Controllers\API\Authorization::class, 'Login']);
            $Router->post('/register', [App\Controllers\API\Authorization::class, 'Register']);
        });
    }
);

Bin::Router($Router);
