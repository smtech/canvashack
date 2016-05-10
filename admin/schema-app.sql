/* the table to store user tokens (admin tokens are stored in the AppMetadata table) */
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `consumer_key` varchar(255) NOT NULL DEFAULT '',
  `id` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(255) DEFAULT '',
  `api_url` text,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* add any other tables to support the application logic here */

# Dump of table canvashacks
# ------------------------------------------------------------

CREATE TABLE `canvashacks` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `name` text NOT NULL,
  `abstract` varchar(512) DEFAULT NULL,
  `description` text NOT NULL,
  `authors` text,
  `path` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table css
# ------------------------------------------------------------

CREATE TABLE `css` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `canvashack` varchar(255) NOT NULL DEFAULT '',
  `path` text NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table dom
# ------------------------------------------------------------

CREATE TABLE `dom` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `canvashack` varchar(255) NOT NULL DEFAULT '',
  `page` varchar(32) DEFAULT '',
  `selector` text NOT NULL,
  `event` text NOT NULL,
  `action` text,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `canvashack` (`canvashack`),
  KEY `page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table javascript
# ------------------------------------------------------------

CREATE TABLE `javascript` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `canvashack` varchar(255) NOT NULL DEFAULT '',
  `path` text NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table pages
# ------------------------------------------------------------

CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `canvashack` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(32) NOT NULL DEFAULT '',
  `url` text,
  `pattern` text,
  `include` tinyint(1) NOT NULL DEFAULT '1',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `canvashack` (`canvashack`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table user_tokens
# ------------------------------------------------------------
