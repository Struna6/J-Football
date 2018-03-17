CREATE TABLE USERS
(
    email varchar(255),
    password varchar(50),
    login varchar(15),
    money int DEFAULT 50,
    hide boolean DEFAULT 0,
    active boolean DEFAULT 0,
    confirmation varchar(255),
    PRIMARY KEY (email),
    DEFAULT CHARSET=utf8
);
