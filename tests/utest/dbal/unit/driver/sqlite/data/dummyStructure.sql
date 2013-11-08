DROP TABLE IF EXISTS tests_comment;
CREATE TABLE tests_comment (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  post_id INTEGER NOT NULL,
  date TEXT NOT NULL,
  comment TEXT NOT NULL
);

CREATE INDEX user_id_post_id ON tests_comment( user_id, post_id );

INSERT INTO tests_comment (id, user_id, post_id, date, comment) VALUES(1, 3, 1, '2011-07-03 00:00:00', '1 comment from user3 for post1');
INSERT INTO tests_comment (id, user_id, post_id, date, comment) VALUES(2, 3, 1, '2012-07-03 00:00:00', '2 comment from user3 for post1');
INSERT INTO tests_comment (id, user_id, post_id, date, comment) VALUES(3, 1, 2, '2011-07-03 00:00:00', '1 comment from user1 for post2');
INSERT INTO tests_comment (id, user_id, post_id, date, comment) VALUES(4, 1, 2, '2012-07-03 00:00:00', '2 comment from user1 for post2');

DROP TABLE IF EXISTS tests_post;
CREATE TABLE tests_post (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  date TEXT NOT NULL,
  post TEXT NOT NULL,
  latest_comment_id INTEGER
);

CREATE INDEX user_id ON tests_post( user_id );

INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(1, 1, '2012-07-03 00:00:00', '1 post from user1', 2);
INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(2, 1, '2012-07-03 00:00:00', '2 post from user1', 4);
INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(3, 2, '2012-06-12 00:00:00', '1 post from user2', 100500);
INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(4, 1, '2012-07-03 00:00:00', '3 post from user1', NULL);
INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(5, 2, '2012-06-12 00:00:00', '2 post from user2', NULL);
INSERT INTO tests_post (id, user_id, date, post, latest_comment_id) VALUES(6, 100500, '2012-06-12 00:00:00', 'broken post: owner is not exists', NULL);


DROP TABLE IF EXISTS tests_user;
CREATE TABLE tests_user (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  login TEXT NOT NULL
);

INSERT INTO tests_user (id, login) VALUES(1, 'user1');
INSERT INTO tests_user (id, login) VALUES(2, 'user2');
INSERT INTO tests_user (id, login) VALUES(3, 'user3');
INSERT INTO tests_user (id, login) VALUES(4, 'user4');