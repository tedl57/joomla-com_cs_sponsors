CREATE TABLE IF NOT EXISTS `#__cs_businesses` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `memid` int(11) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT NULL,
  `image_name` text COLLATE utf8mb4_unicode_ci,
  `listing_description` longtext COLLATE utf8mb4_unicode_ci,
  `show_listing` int(11) DEFAULT NULL,
  `exclude_fields` text COLLATE utf8mb4_unicode_ci,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
