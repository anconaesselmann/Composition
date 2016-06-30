CREATE TABLE connections_activity (
    activity_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NOT NULL,
    action        TINYINT DEFAULT NULL, -- 1:initiated, 2:reciprocated
    row_id        INT UNSIGNED DEFAULT NULL,
    activity_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,

    PRIMARY KEY (activity_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)             ON DELETE CASCADE,
    FOREIGN KEY (row_id)  REFERENCES connections(connection_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE messages_activity (
    activity_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NOT NULL,
    action        TINYINT DEFAULT NULL, -- 3:sent, 4:received
    row_id        INT UNSIGNED DEFAULT NULL,
    activity_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,

    PRIMARY KEY (activity_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)       ON DELETE CASCADE,
    FOREIGN KEY (row_id)  REFERENCES messages(message_id) ON DELETE CASCADE
) ENGINE = InnoDB;