<?php

ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

require_once __DIR__ . '/config/settings.php';


$app->get('/', function () use ($twig) {
            return $twig->render('web/index.html.twig', array());
        });

$app->get('/software', function () use ($twig) {
            return $twig->render('web/software.html.twig', array());
        });

$app->get('/movies', function () use ($db, $twig, $RATINGS_URL, $OMDB_URL) {
            return $twig->render('web/movies.html.twig', array());
        });
        
$app->get('/movies/smatcrufnui', function () use ($db, $twig) {
            return $twig->render('smatcrufnui/index.html.twig', array());
        });


$app->get('/update/imdb/ratings', function() use ($db, $OMDB_URL, $RATINGS_URL) {
            $xml = simplexml_load_file($RATINGS_URL);
            $movies = $xml->xpath('channel/item');
            $ratingFirstIndex = strlen('krsarmiento rated this ');
            $codeFirstIndex = strlen('http://www.imdb.com/title/');

            //Searching movies
            $sql = "SELECT count(*) FROM movies";
            $result = $db->fetchAssoc($sql, array((int) 1));

            $current = (int) $result['count(*)'];
            $new = count($movies);

            if ($new > $current) {
                echo "Saving new " . ($new - $current) . " ratings from IMDb: ";

                for ($index = 0; $index < ($new - $current); $index++) {
                    $code = substr(trim($movies[$index]->link), $codeFirstIndex, -1);
                    $data = file_get_contents($OMDB_URL . $code);
                    $movieData = json_decode($data);

                    $movieData = array(
                        'code' => $code,
                        'title' => $movieData->Title,
                        'my_rating' => substr(trim($movies[$index]->description), $ratingFirstIndex, -1),
                        'imdb_rating' => (float) $movieData->imdbRating,
                        'metascore' => (int) $movieData->Metascore,
                        'year' => $movieData->Year,
                        'genre' => str_replace(", ", ",", $movieData->Genre),
                        'director' => str_replace(", ", ",", $movieData->Director),
                        'actors' => str_replace(", ", ",", $movieData->Actors),
                        'plot' => $movieData->Plot,
                        'poster' => $movieData->Poster
                    );

                    $db->insert('movies', $movieData);
                }
                updateStatistics($db);
            } else {
                echo "Nothing to install ...";
            }

            return "Bye!";
        });

$app->run();


//Functions
function updateStatistics($db) {
    $tableName = 'statistics';

    //Removing old values
    $db->executeUpdate('DELETE FROM ' . $tableName . ' WHERE 1');

    //Total movies
    $sql = "SELECT count(*) FROM movies";
    $result = $db->fetchAssoc($sql, array((int) 1));
    $db->insert($tableName, array('name' => 'allMovies', 'value' => (int) $result['count(*)']));

    //Most viewed actor
    $sql = "SELECT actors FROM movies";
    $movies = $db->fetchAll($sql);
    $db->insert($tableName, array('name' => 'mostViewedActor', 'value' => getHighest('actors', $movies)));

    //Most viewed director
    $sql = "SELECT director FROM movies";
    $movies = $db->fetchAll($sql);
    $db->insert($tableName, array('name' => 'mostViewedDirector', 'value' => getHighest('director', $movies)));
}

function getHighest($name, $movies) {
    $elements = array();

    foreach ($movies as $movie) {
        foreach (split(',', $movie[$name]) as $element) {
            if (array_key_exists($element, $elements))
                $elements[$element] += 1;
            else
                $elements[$element] = 1;
        }
    }

    arsort($elements);
    reset($elements);
    return key($elements);
}
