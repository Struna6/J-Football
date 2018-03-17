CREATE TABLE MATCHES
(
    match_id int AUTO_INCREMENT,
    league varchar(100),
    week int,
    home varchar(100),
    away varchar(100),
    homeScore varchar(100),
    awayScore varchar(100),
    win double,
    draw double,
    lose double,
    start DATETIME,
    PRIMARY KEY(match_id)
);