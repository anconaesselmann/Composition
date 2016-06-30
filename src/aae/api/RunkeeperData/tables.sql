/*

REQUIREMENTS:
A procedure set_userId(userEmail) that sets the user-defined-var @_userId
A table called `users` with a unique key `user_id`

*/

CREATE TABLE runkeeper_login (
    user_id       INT UNSIGNED NOT NULL,
    user_name     VARCHAR(255) NOT NULL,
    user_email    VARCHAR(255) NOT NULL,
    user_password VARCHAR(255) NOT NULL,

    PRIMARY KEY (user_id),

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE runkeeper_sync (
    user_id                 INT UNSIGNED NOT NULL,
    last_logged_date        TIMESTAMP,
    last_logged_activity_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (user_id),

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE activities (
    activity_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id                 INT UNSIGNED NOT NULL,
    activity_date           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type                    TINYINT,
    date_created            TIMESTAMP,

    PRIMARY KEY (activity_id),

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE tracks (
    track_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id              INT UNSIGNED NOT NULL, # remove!!!
    start_time           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time             TIMESTAMP,

    PRIMARY KEY (track_id),

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TABLE points (
    point_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    latitude             DOUBLE NOT NULL,
    longitude            DOUBLE NOT NULL,
    elevation            DOUBLE NOT NULL,
    time                 TIMESTAMP NOT NULL,

    PRIMARY KEY (point_id)
) ENGINE = InnoDB;


CREATE TABLE sequences (
    sequence_id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    track_id             INT UNSIGNED NOT NULL,
    sequence_nbr         INT UNSIGNED NOT NULL,

    PRIMARY KEY (sequence_id),

    UNIQUE (track_id, sequence_nbr),

    FOREIGN KEY (track_id) REFERENCES tracks(track_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE sequence_points (
    point_id             INT UNSIGNED NOT NULL,
    sequence_id          INT UNSIGNED NOT NULL,

    PRIMARY KEY (point_id, sequence_id),

    FOREIGN KEY (point_id)    REFERENCES points(point_id)       ON DELETE CASCADE,
    FOREIGN KEY (sequence_id) REFERENCES sequences(sequence_id) ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TABLE grids (
    grid_id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    point_id          INT UNSIGNED NOT NULL,
    level             INT UNSIGNED NOT NULL DEFAULT 1,

    PRIMARY KEY (grid_id, point_id),

    INDEX (level),
    INDEX (point_id),

    FOREIGN KEY (point_id) REFERENCES points(point_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE sub_grids (
    grid_id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sub_grid_id       INT UNSIGNED NOT NULL,

    PRIMARY KEY (grid_id, sub_grid_id),

    FOREIGN KEY (grid_id)     REFERENCES grids(grid_id) ON DELETE CASCADE,
    FOREIGN KEY (sub_grid_id) REFERENCES grids(grid_id) ON DELETE CASCADE
) ENGINE = InnoDB;
