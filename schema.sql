/* the table to store user tokens (admin tokens are stored in the AppMetadata table) */
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `consumer_key` varchar(255) NOT NULL DEFAULT '',
  `id` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(255) DEFAULT '',
  `api_endpoint` text,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* add any other tables to support the application logic here */