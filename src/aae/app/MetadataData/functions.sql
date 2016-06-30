DELIMITER //

CREATE PROCEDURE getMetadata(userId INT UNSIGNED, tableName VARCHAR(25), meta_id INT UNSIGNED)
	BEGIN
		DECLARE EXIT HANDLER FOR SQLSTATE '42000' SELECT 'SQLException invoked';
		SET @SQL = CONCAT(
			'SELECT * FROM ',tableName,' WHERE creator_id = ',userId,' AND id = ',meta_id);

		PREPARE stmt FROM @SQL;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		SELECT LAST_INSERT_ID();
	END //

CREATE PROCEDURE createMetadata(userId INT UNSIGNED, tableName VARCHAR(25), other TEXT)
	BEGIN
		SET @SQL = CONCAT(
			'INSERT INTO ',tableName,' VALUES(NULL, ',userId,', "',other,'", NULL, NULL)');

		PREPARE stmt FROM @SQL;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		SELECT LAST_INSERT_ID() AS meta_id;
	END //

CREATE PROCEDURE createMetadataTable(dataName VARCHAR(16))
	BEGIN
		SET @SQL = CONCAT(
			'CREATE TABLE IF NOT EXISTS ',dataName,'_metadata (
				id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
				creator_id   INT UNSIGNED NOT NULL,
				other        TEXT NULL DEFAULT NULL,
				date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				date_changed TIMESTAMP NULL,

				PRIMARY KEY (id),
				FOREIGN KEY (creator_id) REFERENCES users(user_id) ON DELETE CASCADE
			) ENGINE = InnoDB'
		);

		PREPARE stmt FROM @SQL;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		SELECT ROW_COUNT();
	END //

DELIMITER ;