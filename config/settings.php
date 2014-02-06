<?php

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'freelance',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8',
    ),
));


$app['twig'] = $app->share(
    $app->extend('twig', function($twig, $app) {
        $twig->addGlobal('assets', '/freelance/assets');
        return $twig;
    }));

$twig = $app['twig'];
$db = $app['db'];

$RATINGS_URL = 'http://rss.imdb.com/user/ur39680938/ratings';
$OMDB_URL = 'http://www.omdbapi.com/?i=';