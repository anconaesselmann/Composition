CREATE TABLE users (
	user_id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_name     VARCHAR(255) NOT NULL,
	user_password VARCHAR(255),
	data          VARCHAR(255),

	PRIMARY KEY (user_id)
) ENGINE = InnoDB;

CREATE TABLE settings (
	settings_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	s1 BOOLEAN DEFAULT FALSE,
	s2 BOOLEAN DEFAULT FALSE,
	s3 BOOLEAN DEFAULT FALSE,

	PRIMARY KEY (settings_id)
) ENGINE = InnoDB;

INSERT INTO users VALUES(NULL, "a", "abc", "value1");
INSERT INTO users VALUES(NULL, "b", "def", "value2");
INSERT INTO users VALUES(NULL, "c", "ghi", "value1");
INSERT INTO users VALUES(NULL, "d", "jkl", "value2");
INSERT INTO users VALUES(NULL, "e", "mno", "value3");
INSERT INTO users VALUES(NULL, "f", "pqr", "value1");


DELIMITER //

CREATE FUNCTION setSettings(s1 BOOLEAN, s2 BOOLEAN, s3 BOOLEAN)
	RETURNS BOOLEAN
	BEGIN
		INSERT INTO settings VALUES(NULL, s1, s2, s3);
		RETURN TRUE;
	END //


CREATE PROCEDURE getUsers(IN _data VARCHAR(255))
	BEGIN
		SELECT *
			FROM users
			WHERE data = _data;
	END //
CREATE PROCEDURE getUsersNames()
	BEGIN
		SELECT user_name FROM users;
	END //

CREATE FUNCTION createUser(userName VARCHAR(255), userPassword VARCHAR(255))
	RETURNS BOOLEAN
	BEGIN
		INSERT INTO users
			VALUES(NULL, userName, userPassword, "aString");
		RETURN TRUE;
	END //

DELIMITER ;