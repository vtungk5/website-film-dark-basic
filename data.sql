CREATE TABLE IF NOT EXISTS `users` (
    `uid` int(11) NOT NULL AUTO_INCREMENT,
    `avatar` text NOT NULL ,
    `name` text NOT NULL,
    `phone` varchar(255) NULL,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,  
    `type` varchar(255) NOT NULL,
    `verify` varchar(255) NOT NULL DEFAULT 0,
    `point` varchar(255) NOT NULL DEFAULT 0,
    `level` varchar(255) NOT NULL,
    `date` text NOT NULL,
    `status` varchar(255) NOT NULL,
    PRIMARY KEY (`uid`)
)ENGINE=`InnoDB` DEFAULT CHARSET=`utf8` AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `film` (
    `IDFilm` int(11) NOT NULL AUTO_INCREMENT,
    `thumbnail` text NOT NULL ,
    `name` text NOT NULL,
    `slug` text NOT NULL,
    `category` varchar(255) NOT NULL,
    `note` varchar(255) NOT NULL,
    `content` varchar(255) NOT NULL,
    `embed` text NOT NULL,
    `date` text NOT NULL,
    `status` varchar(255) NOT NULL,
    PRIMARY KEY (`IDFilm`)
)ENGINE=`InnoDB` DEFAULT CHARSET=`utf8` AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `part` (
    `IDPart` int(11) NOT NULL AUTO_INCREMENT,
    `IDFilm` text NOT NULL ,
    `name` text NOT NULL,
    `date` text NOT NULL,
    PRIMARY KEY (`IDPart`)
)ENGINE=`InnoDB` DEFAULT CHARSET=`utf8` AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `video` (
    `IDVideo` int(11) NOT NULL AUTO_INCREMENT,
    `IDFilm` text NOT NULL ,
    `IDPart`text NOT NULL ,
    `thumbnail` text NOT NULL ,
    `name` text NOT NULL,
    `file` text NULL,
    `episode` text NULL,
    `date` text NOT NULL,
    PRIMARY KEY (`IDVideo`)
)ENGINE=`InnoDB` DEFAULT CHARSET=`utf8` AUTO_INCREMENT=1 ;