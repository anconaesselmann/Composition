CREATE TABLE trust_settings (
    id INT UNSIGNED NOT NULL,
    last_calculated TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    allow_update_after_seconds INT UNSIGNED DEFAULT 1800 NOT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO trust_settings VALUES(1, CURRENT_TIMESTAMP, 1800);

CREATE TABLE user_trust_time (
    user_id                INT UNSIGNED NOT NULL,
    trust_time_given       INT UNSIGNED NOT NULL DEFAULT 0,
    trust_time_gotten      INT UNSIGNED NOT NULL DEFAULT 0,
    temp_trust_time_given  INT UNSIGNED NOT NULL DEFAULT 0,
    temp_trust_time_gotten INT UNSIGNED NOT NULL DEFAULT 0,
    trust_time_final       INT UNSIGNED NOT NULL DEFAULT 0,
    last_calculated        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE connection_trust (
    connection_id    INT UNSIGNED    NOT NULL,
    giver_id         INT UNSIGNED    NOT NULL,
    points_invested  INT UNSIGNED    NOT NULL DEFAULT 0,
    total_trust_time BIGINT UNSIGNED NOT NULL DEFAULT 0,
    last_calculated  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (connection_id, giver_id),
    FOREIGN KEY (connection_id) REFERENCES connections(connection_id) ON DELETE CASCADE,
    FOREIGN KEY (giver_id)      REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;