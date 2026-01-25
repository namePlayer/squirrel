-- Account definition

CREATE TABLE `Account` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `slug` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `username` varchar(50) NOT NULL,
    `passwordHash` varchar(255) NOT NULL,
    `money` int(11) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `Account_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Resource definition

CREATE TABLE `Resource` (
    `uid` varchar(255) NOT NULL,
    PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- AccountResource definition

CREATE TABLE `AccountResource` (
    `account` int(11) NOT NULL,
    `resource` varchar(255) NOT NULL,
    `quantity` int(10) unsigned NOT NULL,
    PRIMARY KEY (`account`,`resource`),
    KEY `AccountResource_Resource_FK` (`resource`),
    CONSTRAINT `AccountResource_Account_FK` FOREIGN KEY (`account`) REFERENCES `Account` (`id`),
    CONSTRAINT `AccountResource_Resource_FK` FOREIGN KEY (`resource`) REFERENCES `Resource` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Merchant definition

CREATE TABLE `Merchant` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `resource` varchar(255) NOT NULL,
    `price` int(10) unsigned NOT NULL,
    `amount` int(10) unsigned NOT NULL,
    `expires` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `Merchant_Resource_FK` (`resource`),
    CONSTRAINT `Merchant_Resource_FK` FOREIGN KEY (`resource`) REFERENCES `Resource` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
