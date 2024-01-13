<?php
session_start();
require_once __DIR__.'/bin.php';
Bin::load([
    '/vendor/autoload.php'
])::run()::direct([
    'setup',
    'app',
    'routes'
]);