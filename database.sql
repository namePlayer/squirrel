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

CREATE TABLE `Item` (
    `uid` varchar(255) NOT NULL,
    PRIMARY KEY (`uid`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE `AccountItem` (
    id UUID NOT NULL,
    account UUID NULL,
    item varchar(255) NULL,
    quantity INT UNSIGNED NULL,
    CONSTRAINT AccountItem_PK PRIMARY KEY (id),
    CONSTRAINT AccountItem_Account_FK FOREIGN KEY (account) REFERENCES Account(id),
    CONSTRAINT AccountItem_Item_FK FOREIGN KEY (item) REFERENCES db.Item(uid)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
