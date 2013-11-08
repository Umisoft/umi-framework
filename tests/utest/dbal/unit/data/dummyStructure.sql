CREATE TABLE test_users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	login VARCHAR(64) NOT NULL,
	password VARCHAR(64) NOT NULL
);

INSERT INTO test_users(login, password) VALUES ('dummy1', 'pass1');
INSERT INTO test_users(login, password) VALUES ('dummy2', 'pass2');
INSERT INTO test_users(login,password) VALUES ('dummy3', 'pass3');