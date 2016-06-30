CREATE TABLE connections (
	connection_id INT UNSIGNED     NOT NULL AUTO_INCREMENT,
	user_a_id     INT UNSIGNED     NOT NULL,
	user_b_id     INT UNSIGNED     NOT NULL,
	status        TINYINT UNSIGNED NOT NULL,
	date_created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,

	PRIMARY KEY (connection_id),
	FOREIGN KEY (user_a_id) REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (user_b_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE deleted_connections (
	connection_id INT UNSIGNED     NOT NULL AUTO_INCREMENT,
	user_a_id     INT UNSIGNED     NOT NULL,
	user_b_id     INT UNSIGNED     NOT NULL,
	status        TINYINT UNSIGNED NOT NULL,
	date_created  TIMESTAMP NOT NULL,
	date_deleted  TIMESTAMP NOT NULL,

	PRIMARY KEY (connection_id),
	FOREIGN KEY (user_a_id) REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (user_b_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE initiated_connections (
	init_connection_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	initiator_id       INT UNSIGNED NOT NULL,
	init_code          CHAR(255)    NOT NULL,
	connection_id      INT UNSIGNED DEFAULT NULL,
	connection_details TEXT NOT NULL,
	init_date          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP NOT NULL,

	PRIMARY KEY (init_connection_id),
	FOREIGN KEY (initiator_id)  REFERENCES users(user_id)             ON DELETE CASCADE,
	FOREIGN KEY (connection_id) REFERENCES connections(connection_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE connection_details (
	connection_id   INT UNSIGNED NOT NULL,
	user_id         INT UNSIGNED NOT NULL,
	alias           VARCHAR(255) DEFAULT "" NOT NULL,
	can_be_messaged BOOLEAN DEFAULT FALSE NOT NULL,
	show_real_name  BOOLEAN DEFAULT FALSE NOT NULL,
	show_user_name  BOOLEAN DEFAULT FALSE NOT NULL,
	show_alias      BOOLEAN DEFAULT FALSE NOT NULL,
	show_email      BOOLEAN DEFAULT FALSE NOT NULL,
	show_phone      BOOLEAN DEFAULT FALSE NOT NULL,
	show_address    BOOLEAN DEFAULT FALSE NOT NULL,

	PRIMARY KEY (connection_id, user_id),
	FOREIGN KEY (connection_id) REFERENCES connections(connection_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id)       REFERENCES users(user_id)             ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE deleted_connection_details (
	connection_id   INT UNSIGNED NOT NULL,
	user_id         INT UNSIGNED NOT NULL,
	alias           VARCHAR(255) DEFAULT "" NOT NULL,
	can_be_messaged BOOLEAN DEFAULT FALSE NOT NULL,
	show_real_name  BOOLEAN DEFAULT FALSE NOT NULL,
	show_user_name  BOOLEAN DEFAULT FALSE NOT NULL,
	show_alias      BOOLEAN DEFAULT FALSE NOT NULL,
	show_email      BOOLEAN DEFAULT FALSE NOT NULL,
	show_phone      BOOLEAN DEFAULT FALSE NOT NULL,
	show_address    BOOLEAN DEFAULT FALSE NOT NULL,

	PRIMARY KEY (connection_id, user_id),
	FOREIGN KEY (connection_id) REFERENCES deleted_connections(connection_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id)       REFERENCES users(user_id)             ON DELETE CASCADE
) ENGINE = InnoDB;