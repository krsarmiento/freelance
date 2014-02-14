<?php

use Symfony\Component\HttpFoundation\Response;

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

	$mostViewed = array( array('id' => 'mvactors', 'label' => 'Most Viewed Actors', 'entities' => array()), array('id' => 'mvdirectors', 'label' => 'Most Viewed Directors', 'entities' => array()), array('id' => 'mvdecade', 'label' => 'Most Viewed Decades', 'entities' => array()), array('id' => 'mvgenre', 'label' => 'Most Viewed Genres', 'entities' => array()), );

	$data = array('ratingScores' => $ratingScores, 'mostViewed' => $mostViewed);

	return $twig -> render('smatcrufnui/index.html.twig', $data);
});

$app -> get('/ajax/movies/load/{rating}/{times}', function($rating, $times) use ($db, $twig, $MOVIE_LIMIT) {
	$data = array(
		'movies' => getMovies($rating, $times, $MOVIE_LIMIT)
	);

	return $twig -> render('smatcrufnui/ajax_load_movies.html.twig', $data);
});



$app -> get('/update/imdb/ratings', function() use ($db, $OMDB_URL, $RATINGS_URL) {
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

			$movieData = array('code' => $code, 'title' => $movieData -> Title, 'my_rating' => substr(trim($movies[$index] -> description), $ratingFirstIndex, -1), 'imdb_rating' => (float)$movieData -> imdbRating, 'metascore' => (int)$movieData -> Metascore, 'year' => $movieData -> Year, 'genre' => str_replace(", ", ",", $movieData -> Genre), 'director' => str_replace(", ", ",", $movieData -> Director), 'actors' => str_replace(", ", ",", $movieData -> Actors), 'plot' => $movieData -> Plot, 'poster' => $movieData -> Poster);

			$db -> insert('movies', $movieData);
		}
		updateStatistics($db);
	} else {
		echo "Nothing to install ...";
	}

	return "Bye!";
});
