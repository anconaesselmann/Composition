CREATE TABLE users (
	user_id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_name     VARCHAR(255) NOT NULL,
	user_password VARCHAR(255) NOT NULL,
	user_email    VARCHAR(255) NOT NULL,
	user_created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	password_age  TIMESTAMP NOT NULL,
	user_status   TINYINT DEFAULT 0 NOT NULL,

	PRIMARY KEY (user_id),
	UNIQUE KEY  (user_name),
	UNIQUE KEY  (user_email)
) ENGINE = InnoDB;

CREATE TABLE user_details (
	user_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(255),
    last_name  VARCHAR(255),
    phone_nbr  VARCHAR(255),
    address    VARCHAR(255),
    city       VARCHAR(255),
    zip        VARCHAR(255),
    state      VARCHAR(255),
    country    VARCHAR(255),

    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE application_settings (
	application_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	seconds_between_login_attempts INT UNSIGNED DEFAULT 0 NOT NULL,
	seconds_reset_codes_valid      INT UNSIGNED DEFAULT 0 NOT NULL,
	PRIMARY KEY (application_id)
) ENGINE = InnoDB;

CREATE TABLE reset_codes (
	reset_id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id     INT UNSIGNED NOT NULL,
	reset_code  CHAR(255)    NOT NULL,
	reset_time  TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	remote_addr VARCHAR(45) DEFAULT NULL,

	PRIMARY KEY (reset_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE persistent_logins (
	series_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id        INT UNSIGNED NOT NULL,
	device_name    VARCHAR(255) DEFAULT "" NOT NULL,
	browser_info   BLOB NOT NULL,
	rand_code_hash VARCHAR(255) NOT NULL,

	PRIMARY KEY (series_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE logins (
	login_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id       INT UNSIGNED NOT NULL,
	login_time    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	login_success BOOLEAN DEFAULT FALSE NOT NULL,
	login_type    TINYINT DEFAULT 1     NOT NULL,
	remote_addr   VARCHAR(45) DEFAULT NULL,

	PRIMARY KEY (login_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE old_passwords (
	password_id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id      INT UNSIGNED NOT NULL,
	old_password VARCHAR(255) NOT NULL,
	date_changed TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	remote_addr  VARCHAR(45) DEFAULT NULL,

	PRIMARY KEY (password_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;