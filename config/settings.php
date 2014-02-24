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
$MOST_VIEWED_MAX = 8;

$SCORE_RANGES = array(
    100, 95, 90, 85, 80, 70
);

$HIGHEST_SCORE = 10;
$MOVIE_LIMIT = 8;
$IMDB_MOVIE_URL = 'http://www.imdb.com/title/';
$IMDB_NAME_URL = 'http://www.imdb.com/name/';
$IMDB_FIND_URL = 'http://www.imdb.com/xml/find?json=1&nr=1&nm=on&q=';

$IMAGE_STATISTICS = array('mostViewedActors', 'mostViewedDirectors', 'favoriteActors', 'favoriteActresses');

$FAVORITE_ACTORS = array('Jeremy Renner', 'Denzel Washington', 'Kevin Spacey', 'Clint Eastwood');
$FAVORITE_ACTRESSES = array('Julie Delpy', 'Rooney Mara', 'Charlotte Gainsbourg', 'Uma Thurman');