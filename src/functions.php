<?php

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
    $db->insert($tableName, array('name' => 'mostViewedActors', 'value' => getHighest('actors', $movies)));

    //Most viewed director
    $sql = "SELECT director FROM movies";
    $movies = $db->fetchAll($sql);
    $db->insert($tableName, array('name' => 'mostViewedDirectors', 'value' => getHighest('director', $movies)));
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