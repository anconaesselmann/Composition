/*

REQUIREMENTS:
A procedure set_userId(userEmail) that sets the user-defined-var @_userId
A table called `users` with a unique key `user_id`

*/

CREATE TABLE reputation_points (
	user_id INT UNSIGNED NOT NULL,
	points  INT UNSIGNED NOT NULL DEFAULT 0,
	last_calculated TIMESTAMP,

	PRIMARY KEY (user_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

/* TODO: remove event_type and add table and access function that is specific
for the type of activity that is tracked. */
CREATE TABLE reputation_events (
	event_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	event_type     TINYINT UNSIGNED NOT NULL,
	beneficiary_id INT     UNSIGNED NOT NULL,
	benefactor_id  INT     UNSIGNED NOT NULL,
	event_time     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	last_counted   TIMESTAMP NULL DEFAULT NULL,

	PRIMARY KEY (event_id),
	FOREIGN KEY (beneficiary_id) REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (benefactor_id)  REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;