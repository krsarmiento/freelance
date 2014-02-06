<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

require_once __DIR__.'/config/settings.php';




$app->get('/', function () use ($twig) {
    return $twig->render('web/index.html.twig', array());
});

$app->get('/software', function () use ($twig) {
    return $twig->render('web/software.html.twig', array());
});

$app->get('/movies', function () use ($db, $twig, $RATINGS_URL, $OMDB_URL) {

    
    

    
    return "fuck you!";
});

/*
 * TABLA MOVIES
 * - ID
 * - CODE
 * - TITLE
 * - RATING
 * - DATA
 */


$app->get('/update/imdb/ratings', function() use ($db, $OMDB_URL, $RATINGS_URL) {
    $xml = simplexml_load_file($RATINGS_URL); 
    $movies = $xml->xpath('channel/item');
    $ratingFirstIndex = strlen('krsarmiento rated this ');
    $codeFirstIndex = strlen('http://www.imdb.com/title/');
    
    //Searching movies
    $sql = "SELECT count(*) FROM movies";
    $result = $db->fetchAssoc($sql, array((int) 1));

    $current = (int)$result['count(*)'];
    $new = count($movies);
    
    if ($new > $current) {
        echo "Saving new " . ($new-$current) . " ratings from IMDb: ";
        
        for ($index = 0; $index < ($new-$current); $index++) {
            $code = substr(trim($movies[$index]->link), $codeFirstIndex, -1);
            
            $movieData = array(
                'code'      => $code,
                'title'     => trim($movies[$index]->title),
                'rating'    => substr(trim($movies[$index]->description), $ratingFirstIndex, -1),
                'data'      => file_get_contents($OMDB_URL . $code)
            );
            
            $db->insert('movies', $movieData);
            echo ".";
        }
    } else {
        echo "Nothing to install ...";
    }
    
    
    return "Bye!";
    
//
//    return  "<h1>{$post['title']}</h1>".
//            "<p>{$post['body']}</p>";
//    
//    foreach( $movies as $movie ) {
//        $title = trim($movie->title);
//        $rating = substr(trim($movie->description), $ratingFirstIndex, -1);
//        $code = substr(trim($movie->link), $codeFirstIndex, -1);
//    }
    
});

$app->run();
