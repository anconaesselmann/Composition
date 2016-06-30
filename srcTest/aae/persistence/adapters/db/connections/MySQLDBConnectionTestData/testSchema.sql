CREATE TEMPORARY TABLE phpUnit_db.tableA (
    tableA_item_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    tableA_item_name VARCHAR(255) NOT NULL,
    tableA_item_text TEXT NOT NULL,

    PRIMARY KEY (tableA_item_id)
) ENGINE = InnoDB, COMMENT = 'Test table a.';

CREATE TEMPORARY TABLE phpUnit_db.tableB (
    tableB_item_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    tableA_item_id INT UNSIGNED NOT NULL,
    tableB_item_name VARCHAR(255) NOT NULL,
    tableB_item_text TEXT NOT NULL,

    PRIMARY KEY (tableB_item_id),

    FOREIGN KEY (tableA_item_id) REFERENCES phpUnit_db.tableA(tableA_item_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = InnoDB, COMMENT = 'Holds all challenges.';