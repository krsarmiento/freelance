<?php
require_once __DIR__.'/vendor/autoload.php';


$app = new Silex\Application();

// SETTINGS
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


$app['twig'] = $app->share(
    $app->extend('twig', function($twig, $app) {
        $twig->addGlobal('assets', 'assets');
        return $twig;
    }));

$twig = $app['twig'];


// CONTROLLERS
$app->get('/', function () use ($twig) {
    return $twig->render('web/index.html.twig', array());
});

$app->run();
