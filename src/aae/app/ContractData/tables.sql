CREATE TABLE contracts (
	contract_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
	contractor_id INT UNSIGNED NOT NULL,
	contractee_id INT UNSIGNED NOT NULL,
	contract_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY (contract_id),
	FOREIGN KEY (contractor_id) REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (contractee_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE exposed_info (
	contract_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id     INT UNSIGNED NOT NULL,
	info_name   VARCHAR(100) NOT NULL,

	PRIMARY KEY (contract_id),
	FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id)     REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE initiated_contracts (
	init_contract_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	initiator_id INT UNSIGNED NOT NULL,
	init_code CHAR(255) NOT NULL,
	contract_id INT UNSIGNED DEFAULT NULL,
	init_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY (init_contract_id),
	FOREIGN KEY (initiator_id) REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (contract_id)  REFERENCES contracts(contract_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE relationships (
	user_id           INT UNSIGNED NOT NULL,
	associate_id      INT UNSIGNED NOT NULL,
	relationship_type TINYINT UNSIGNED NOT NULL,
	date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY (user_id, associate_id),
	FOREIGN KEY (user_id)       REFERENCES users(user_id) ON DELETE CASCADE,
	FOREIGN KEY (associate_id)  REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;