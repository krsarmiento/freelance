CREATE DATABASE freelance;

USE freelance;

CREATE TABLE IF NOT EXISTS `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `title` text NOT NULL,
  `my_rating` float NOT NULL,
  `imdb_rating` float NOT NULL,
  `metascore` tinyint(2) NOT NULL,
  `year` smallint(4) NOT NULL,
  `genre` text NOT NULL,
  `director` text NOT NULL,
  `actors` text NOT NULL,
  `plot` text NOT NULL,
  `poster` text NOT NULL,
  `ranking` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;