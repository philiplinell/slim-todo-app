DROP TABLE IF exists users;

CREATE TABLE users(
       user_id             INTEGER PRIMARY KEY,
       user_name           TEXT NOT NULL UNIQUE,
       user_email          TEXT NOT NULL UNIQUE,
       user_pw             TEXT NOT NULL,
       created_at          TEXT,
       updated_at          TEXT
);

DROP TABLE IF exists todos;

CREATE TABLE todos(
       todo_done        INTEGER NOT NULL,
       todo_description TEXT NOT NULL,
       todo_done_at     TEXT,
       user_id          INTEGER,
       FOREIGN KEY(user_id) REFERENCES users(user_id)
);
