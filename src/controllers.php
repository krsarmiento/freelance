<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sunra\PhpSimple\HtmlDomParser;

require_once __DIR__ . '/functions.php';

$app -> get('/', function() use ($twig) {
	return $twig -> render('web/index.html.twig', array());
});

$app -> get('/software', function() use ($twig) {
	return $twig -> render('web/software.html.twig', array());
});

$app -> get('/load/poster/{id}', function($id) use ($twig) {
	$movie = getMovie($id);
	return new Response(getPoster($movie['poster']), 200, array('Content-type' => 'image/jpg'));
});

$app -> get('/movies', function() use ($db, $twig, $RATINGS_URL, $OMDB_URL, $SCORE_RANGES, $HIGHEST_SCORE, $MOVIE_LIMIT) {
	$ratingScores = array();

	foreach ($SCORE_RANGES as $score) {
		$rating = (float)($score / $HIGHEST_SCORE);
		$ratingScores[] = array(
			'id' => 'collapse' . $score, 
			'float_rating' => $rating, 
			'rating' => $rating . '/' . $HIGHEST_SCORE, 
			'movies' => getMovies($rating, 1, $MOVIE_LIMIT)
		);
	}
        
        $favorites = array(
            array('id' => 'favactors', 'label' => 'Favorite Actors', 'entities' => getStatistic('favoriteActors')), 
            array('id' => 'favactresses', 'label' => 'Favorite Actresses', 'entities' => getStatistic('favoriteActresses')), 
        );
        
	$mostViewed = array( 
            array('id' => 'mvactors', 'label' => 'Most Viewed Actors', 'entities' => getStatistic('mostViewedActors')), 
            array('id' => 'mvdirectors', 'label' => 'Most Viewed Directors', 'entities' => getStatistic('mostViewedDirectors')), 
        );

	$data = array('ratingScores' => $ratingScores, 'favorites' => $favorites,'mostViewed' => $mostViewed);

	return $twig -> render('smatcrufnui/index.html.twig', $data);
});

$app -> get('/ajax/movies/load/{rating}/{times}', function($rating, $times) use ($db, $twig, $MOVIE_LIMIT) {
        $data = array(
            'movies' => getMovies($rating, $times, $MOVIE_LIMIT)
	);

	return $twig -> render('smatcrufnui/ajax_load_movies.html.twig', $data);
});


$app -> get('/ajax/movie/data/{id}', function($id) use ($twig, $IMDB_MOVIE_URL) {
	$data = array(
		'movie' => getMovie($id),
                'imdb_url' => $IMDB_MOVIE_URL
	);

        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
	return $response;
});



$app -> get('/update/imdb/ratings', function() use ($db, $OMDB_URL, $RATINGS_URL, $HIGHEST_SCORE) {
	$xml = simplexml_load_file($RATINGS_URL);
	$movies = $xml -> xpath('channel/item');
	$ratingFirstIndex = strlen('krsarmiento rated this ');
	$codeFirstIndex = strlen('http://www.imdb.com/title/');

	//Searching movies
	$sql = "SELECT count(*) FROM movies";
	$result = $db -> fetchAssoc($sql, array((int)1));

	$current = (int)$result['count(*)'];
	$new = count($movies);

	if ($new > $current) {
		echo "Saving new " . ($new - $current) . " ratings from IMDb: ";

		for ($index = 0; $index < ($new - $current); $index++) {
			$code = substr(trim($movies[$index] -> link), $codeFirstIndex, -1);
			$data = file_get_contents($OMDB_URL . $code);
			$movieData = json_decode($data);

			$movieData = array(
                            'code' => $code, 
                            'title' => utf8_decode($movieData -> Title), 
                            'my_rating' => (float)substr(trim($movies[$index] -> description), $ratingFirstIndex, -1), 
                            'imdb_rating' => (float)$movieData -> imdbRating, 
                            'metascore' => (float)((int)$movieData->Metascore / $HIGHEST_SCORE), 
                            'year' => $movieData -> Year, 
                            'genre' => str_replace(", ", ",", $movieData -> Genre), 
                            'director' => str_replace(", ", ",", utf8_decode($movieData -> Director)), 
                            'actors' => str_replace(", ", ",", utf8_decode($movieData -> Actors)), 
                            'plot' => $movieData -> Plot, 
                            'poster' => $movieData -> Poster);

			$db -> insert('movies', $movieData);
		}
		updateStatistics($db);
	} else {
		echo "Nothing to install ...";
	}

	return "Bye!";
});


$app -> get('/update/imdb/images', function() use ($db) {
	updateStatisticImages($db);
        return "Images installed.";
});



$app -> get('/test/update', function() use ($db, $OMDB_URL, $RATINGS_URL, $HIGHEST_SCORE) {
    $code = 'tt0245712';
    $xml = simplexml_load_file($RATINGS_URL);
    $movies = $xml -> xpath('channel/item');
    $ratingFirstIndex = strlen('krsarmiento rated this ');
    $data = file_get_contents($OMDB_URL . $code);
    $movieData = json_decode($data);

    $movieData = array(
        'code' => $code, 
        'title' => utf8_decode($movieData -> Title), 
        'my_rating' => 9.99, 
        'imdb_rating' => (float)$movieData -> imdbRating, 
        'metascore' => (float)((int)$movieData->Metascore / $HIGHEST_SCORE), 
        'year' => $movieData -> Year, 
        'genre' => str_replace(", ", ",", $movieData -> Genre), 
        'director' => str_replace(", ", ",", utf8_decode($movieData -> Director)), 
        'actors' => str_replace(", ", ",", utf8_decode($movieData -> Actors)), 
        'plot' => $movieData -> Plot, 
        'poster' => $movieData -> Poster);
    
    var_dump($movieData);
    
    testStatistics($db);
    
    return "Done!";
});
