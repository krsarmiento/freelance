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

$ACHIEVEMENTS = array(
    array(
        'title' => 'Semantic Web', 
        'description' => 'Blog about topics related to the SemWeb. The main topic is the Resource Description Framework (RDF) with its own translated tutorial.',
        'image' => '/freelance/assets/img/rdf.png',
        'url' => 'http://semantizandolaweb.wordpress.com/',
        'license' => 'http://www.picol.org/icon_library.php'
    ),
    array(
        'title' => 'Programming contest', 
        'description' => 'Winner of a programming contest with problems based on the ACIS / REDIS contest. This was in 2° year of Engineering.',
        'image' => '/freelance/assets/img/win.png',
        'url' => 'http://www.acis.org.co/index.php?id=556',
        'license' => 'http://www.wpzoom.com/wpzoom/new-freebie-wpzoom-developer-icon-set-154-free-icons/'
    ),
    array(
        'title' => 'Entrance Examination', 
        'description' => 'Highest score of around 500 students in the 2009-1 entrance examination of the Universidad de Cartagena.',
        'image' => '/freelance/assets/img/college.png',
        'url' => 'http://www.unicartagena.edu.co/',
        'license' => 'https://www.mapbox.com/tilemill/docs/guides/using-maki-icons/'
    ),
    array(
        'title' => 'Company Name', 
        'description' => 'While having vacations with the company crew, I gave Solocontrata.me its new name: Oppten.com.',
        'image' => '/freelance/assets/img/card.png',
        'url' => 'http://oppten.com',
        'license' => 'https://github.com/stephenhutchings/typicons.font'
    ),
);