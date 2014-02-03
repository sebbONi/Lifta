CREATE TABLE `travels` (
	`id` bigint NOT NULL AUTO_INCREMENT,
	`owner` bigint NOT NULL,
	 `start` tinytext NOT NULL,
	`date` tinytext NOT NULL,
	`seats` int NOT NULL,
	PRIMARY KEY (`id`)
);