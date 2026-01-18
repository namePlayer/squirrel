--
-- Database basic layout creation for MariaDB 10.11
--

CREATE TABLE `Account` (
    id UUID NOT NULL,
    email varchar(255) NULL,
    username varchar(50) NULL,
    passwordHash varchar(255) NULL,
    CONSTRAINT Account_PK PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE `Resource` (
    `uid` varchar(255) NOT NULL,
    PRIMARY KEY (`uid`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE `AccountResource` (
    id UUID NOT NULL,
    account UUID NULL,
    item varchar(255) NULL,
    quantity INT UNSIGNED NULL,
    CONSTRAINT AccountResource_PK PRIMARY KEY (id),
    CONSTRAINT AccountResource_Account_FK FOREIGN KEY (account) REFERENCES Account(id),
    CONSTRAINT AccountResource_Item_FK FOREIGN KEY (item) REFERENCES Resource(uid)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
