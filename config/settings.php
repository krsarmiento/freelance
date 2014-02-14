<?php

require_once __DIR__.'/local_settings.php';

$app['debug'] = $DEBUG;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $DB_OPTIONS,
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
$MOST_VIEWED_MAX = 5;

$SCORE_RANGES = array(
    100, 95, 90, 85, 80, 70
);

$HIGHEST_SCORE = 10;
$MOVIE_LIMIT = 8;
$IMDB_MOVIE_URL = 'http://www.imdb.com/title/';