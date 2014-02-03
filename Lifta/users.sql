CREATE TABLE `users` (
  `fbid` bigint NOT NULL,
  `fname` tinytext NOT NULL,
  `lname` tinytext NOT NULL,
  `email` tinytext NOT NULL,
  `rating` float,
  PRIMARY KEY (`fbid`)
);