<?php

use Sunra\PhpSimple\HtmlDomParser;

function updateStatistics($db) {
//    $tableName = 'statistics';
//
//    //Removing old values
//    $db->executeUpdate('DELETE FROM ' . $tableName . ' WHERE 1');
//
//    //Total movies
//    $sql = "SELECT count(*) FROM movies";
//    $result = $db->fetchAssoc($sql, array((int) 1));
//    $db->insert($tableName, array('name' => 'allMovies', 'value' => (int) $result['count(*)']));
//
//    //Most viewed actors
//    $sql = "SELECT actors FROM movies";
//    $movies = $db->fetchAll($sql);
//    $db->insert($tableName, array('name' => 'mostViewedActors', 'value' => getHighest('actors', $movies)));
//
//    //Most viewed directors
//    $sql = "SELECT director FROM movies";
//    $movies = $db->fetchAll($sql);
//    $db->insert($tableName, array('name' => 'mostViewedDirectors', 'value' => getHighest('director', $movies)));
    
    global $IMAGE_STATISTICS;
    foreach ($IMAGE_STATISTICS as $statistic) {
        updateStatisticImages($statistic);
    }
    
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
    
    global $MOST_VIEWED_MAX;
    $result = "";
    arsort($elements);
    reset($elements);
    
    
    for ($i=0; $i<$MOST_VIEWED_MAX; $i++) {
        $result = $result . key($elements) . ",";
        next($elements);
    }

    return substr($result, 0, -1);
}

function getMovie($id) {
    global $db;
    $sql = "SELECT * FROM movies where id=".$id;
    $result = $db->fetchAll($sql);
    return $result[0];
}

function getMovies($rating, $times, $limit) {
    global $db;
	$finalLimit = ($times-1)*$limit . ', ' . $limit;
	
    $sql = "SELECT * FROM movies where my_rating=".$rating." LIMIT ".$finalLimit;
    $movies = $db->fetchAll($sql);
    return $movies;
}

function getStatistic($key) {
    global $db;
    $sql = "SELECT * FROM statistics where name='".$key."'";
    $statistic = $db->fetchAll($sql);
    
    $entities = split(',', $statistic[0]['value']);
         
    return $entities;
}

function getPoster($url) {
    return file_get_contents($url);
}

function convertImage($url){
    $imagedata = file_get_contents($url);
    $base64 = 'data:image/jpg;base64,' . base64_encode($imagedata);
    return $base64;
}

function getImage($name) {
    global $db;
    $sql = "SELECT * FROM images where name='".$name."'";
    $image = $db->fetchAll($sql);
    if ($image)
        return $image[0]['value'];
    else
        return null;
}

function saveImage($name, $imageUrl) {
    global $db;
    $base64 = convertImage($imageUrl);
    $data = array('name' => $name, 'value' => $base64);
    $db -> insert('images', $data);
}


function updateStatisticImages($name) {
    global $IMDB_FIND_URL;
    global $IMDB_NAME_URL;
    
    foreach (getStatistic($name) as $entity) {
        if (!getImage($entity)) {
            $url = $IMDB_FIND_URL . urlencode($entity);
            $response = file_get_contents($url);
            $data = json_decode($response);
            
            if (property_exists($data, name_popular))
                $name = $data->name_popular;
            else
                $name = $data->name_approx;

            $dom = HtmlDomParser::file_get_html( $IMDB_NAME_URL . $name[0]->id );
            $image = $dom->find('img[id=name-poster]', 0);
            saveImage($name[0]->name, $image->attr['src']);
        }
    }
}






function testStatistics($db) {
    //Total movies
    $sql = "SELECT count(*) FROM movies";
    $result = $db->fetchAssoc($sql, array((int) 1));
    var_dump(array('name' => 'allMovies', 'value' => (int) $result['count(*)']));

    //Most viewed actors
    $sql = "SELECT actors FROM movies";
    $movies = $db->fetchAll($sql);
    var_dump($tableName, array('name' => 'mostViewedActors', 'value' => getHighest('actors', $movies)));

    //Most viewed directors
    $sql = "SELECT director FROM movies";
    $movies = $db->fetchAll($sql);
    var_dump($tableName, array('name' => 'mostViewedDirectors', 'value' => getHighest('director', $movies)));
}