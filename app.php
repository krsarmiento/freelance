<?php
require_once __DIR__.'/vendor/autoload.php';


$app = new Silex\Application();

// SETTINGS
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
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


// CONTROLLERS
$app->get('/', function () use ($twig) {
    return $twig->render('web/index.html.twig', array());
});

$app->get('/software', function () use ($twig) {
    return $twig->render('web/software.html.twig', array());
});

$app->get('/movies', function () use ($db, $twig, $RATINGS_URL, $OMDB_URL) {
    $xml = simplexml_load_file($RATINGS_URL); 
    $movies = $xml->xpath('channel/item');
    $ratingFirstIndex = strlen('krsarmiento rated this ');
    $codeFirstIndex = strlen('http://www.imdb.com/title/');
    
    
    $variable = file_get_contents($OMDB_URL . "tt0264464");
    $decoded = json_decode($variable);
    
    echo $variable;
    var_dump($variable);
    var_dump($decoded->Plot);
    
//    $sql = "SELECT * FROM posts WHERE id = ?";
//    $post = $db->fetchAssoc($sql, array((int) 1));
//
//    return  "<h1>{$post['title']}</h1>".
//            "<p>{$post['body']}</p>";
//    
//    foreach( $movies as $movie ) {
//        $title = trim($movie->title);
//        $rating = substr(trim($movie->description), $ratingFirstIndex, -1);
//        $code = substr(trim($movie->link), $codeFirstIndex, -1);
//    }
    
    return "fuck you!";
});

//

$app->run();
