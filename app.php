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
        $twig->addGlobal('assets', '/freelance/assets');
        return $twig;
    }));

$twig = $app['twig'];
$RATINGS_URL = 'http://rss.imdb.com/user/ur39680938/ratings';


// CONTROLLERS
$app->get('/', function () use ($twig) {
    return $twig->render('web/index.html.twig', array());
});

$app->get('/software', function () use ($twig) {
    return $twig->render('web/software.html.twig', array());
});

//<item>
//    <pubDate>Mon, 03 Feb 2014 00:00:00 GMT</pubDate>
//    <title>Lock, Stock and Two Smoking Barrels (1998)</title>
//    <link>http://www.imdb.com/title/tt0120735/</link>
//    <guid>http://www.imdb.com/title/tt0120735/</guid>
//    <description>
//        krsarmiento rated this 8.
//    </description>
//    <pubDate>Mon Feb  3 00:00:00 2014</pubDate>
//</item>

$app->get('/movies', function () use ($twig, $RATINGS_URL) {
    $xml = simplexml_load_file($RATINGS_URL); 
    $movies = $xml->xpath('channel/item');
    $ratingFirstIndex = strlen('krsarmiento rated this ');
    
    
    
    
    foreach( $movies as $movie ) {
        $title = trim($movie->title);
        $description = trim($movie->description);
        $link = trim($movie->link);
        
        echo "---------------------------<br/>";
        echo $title;
        echo substr($description, $ratingFirstIndex, -1);
        echo $title;
        echo "---------------------------<br/>";
    }
    
    return "fuck you!";
});

//

$app->run();
