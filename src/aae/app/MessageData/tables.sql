CREATE TABLE messages (
	message_id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
	sender_id        INT UNSIGNED NOT NULL,
	connection_id    INT UNSIGNED NOT NULL,
	messages_subject VARCHAR(255) NOT NULL,
	messages_body    TEXT NOT NULL,
	message_status   TINYINT DEFAULT 0 NOT NULL,
	time_created     TIMESTAMP NOT NULL,
	time_sent        TIMESTAMP NULL,
	time_read        TIMESTAMP NULL,

	PRIMARY KEY (message_id),
	FOREIGN KEY (sender_id)    REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (connection_id) REFERENCES connections(connection_id) ON DELETE CASCADE
) ENGINE = InnoDB;