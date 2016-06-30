CREATE TABLE cages (
    cage_id INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    creator_id INT UNSIGNED NOT NULL,
    time_created TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cage_name VARCHAR(255)  NOT NULL,

    PRIMARY KEY (cage_id),
    FOREIGN KEY (creator_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE litters (
    litter_id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    mother_id  INT UNSIGNED NOT NULL,
    father_id  INT UNSIGNED NOT NULL,
    birth_date TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (litter_id)
) ENGINE = InnoDB;

CREATE TABLE genotypes (
    genotype_id INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    creator_id INT UNSIGNED    NOT NULL,
    genotype_name VARCHAR(255) NOT NULL,
    genotype_color CHAR(11)    NOT NULL,
    time_created TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (genotype_id),
    FOREIGN KEY (creator_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE mice (
    mouse_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    creator_id INT UNSIGNED NOT NULL,
    sex TINYINT NOT NULL,
    litter_id INT UNSIGNED DEFAULT NULL,
    cage_id INT UNSIGNED DEFAULT NULL,
    genotype_id INT UNSIGNED DEFAULT NULL,
    time_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    time_deceased TIMESTAMP NULL,

    PRIMARY KEY (mouse_id),
    FOREIGN KEY (creator_id)  REFERENCES users(user_id)         ON DELETE CASCADE,
    FOREIGN KEY (litter_id)   REFERENCES litters(litter_id)     ON DELETE CASCADE,
    FOREIGN KEY (cage_id)     REFERENCES cages(cage_id)         ON DELETE CASCADE,
    FOREIGN KEY (genotype_id) REFERENCES genotypes(genotype_id) ON DELETE CASCADE
) ENGINE = InnoDB;

ALTER TABLE litters ADD FOREIGN KEY (mother_id) REFERENCES mice(mouse_id) ON DELETE CASCADE;
ALTER TABLE litters ADD FOREIGN KEY (father_id) REFERENCES mice(mouse_id) ON DELETE CASCADE;