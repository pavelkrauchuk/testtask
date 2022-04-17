<?php
use Pavelkrauchuk\Testtask\Entity\User;
use Pavelkrauchuk\Testtask\Router;

require 'vendor/autoload.php';

session_start();

$router = new Router();
$user = new User();

$path = $router->getPath();

if ($user->isLogged()) {
    $router->get();
} elseif ($path === '/authenticate') {
    $router->get($path);
} else {
    $router->get('/login');
}