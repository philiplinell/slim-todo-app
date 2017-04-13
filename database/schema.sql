DROP TABLE IF exists users;

CREATE TABLE users(
       user_name           TEXT NOT NULL,
       user_pw             TEXT NOT NULL,  
       user_email          TEXT NOT NULL UNIQUE,
       registration_date   TEXT,
       unregistration_date TEXT,
       PRIMARY KEY(user_name)
);
