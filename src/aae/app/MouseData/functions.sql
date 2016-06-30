DELIMITER //

CREATE FUNCTION addCage(userId INT UNSIGNED, cageName VARCHAR(255))
    RETURNS INT UNSIGNED
    BEGIN
        INSERT INTO cages
            VALUES(NULL, userId, NULL, cageName);

        RETURN LAST_INSERT_ID();
    END //

CREATE FUNCTION createLitter(userId INT UNSIGNED, motherId INT UNSIGNED, fatherId INT UNSIGNED, birtDate TIMESTAMP)
    RETURNS INT UNSIGNED
    BEGIN
        INSERT INTO litters
            VALUES(NULL, motherId, fatherId, birtDate);

        RETURN LAST_INSERT_ID();
    END //
CREATE FUNCTION createGenotype(userId INT UNSIGNED, genotypeName VARCHAR(255), color CHAR(11))
    RETURNS INT UNSIGNED
    BEGIN
        INSERT INTO genotypes
            VALUES(NULL, userId, genotypeName, color, NULL);

        RETURN LAST_INSERT_ID();
    END //

CREATE FUNCTION removeCage(userId INT UNSIGNED, cageId VARCHAR(255))
    RETURNS INT
    BEGIN
        DELETE FROM cages
        WHERE creator_id = userId
            AND cage_id = cageId;

        RETURN ROW_COUNT();
    END //
CREATE FUNCTION deleteMouse(userId INT UNSIGNED, mouseId VARCHAR(255))
    RETURNS INT
    BEGIN
        DELETE FROM mice
        WHERE creator_id = userId
            AND mouse_id = mouseId;

        RETURN ROW_COUNT();
    END //

CREATE FUNCTION cageCount(userId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        DECLARE result INT DEFAULT 0;
        SELECT COUNT(*)
            INTO result
            FROM cages
            WHERE creator_id = userId;

        RETURN result;
    END //

CREATE FUNCTION newMouse(userId INT UNSIGNED, sex TINYINT, litterId INT UNSIGNED, cageId INT UNSIGNED, genotypeId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        INSERT INTO mice
            VALUES(NULL, userId, sex, litterId, cageId, genotypeId, NULL, NULL);

        RETURN LAST_INSERT_ID();
    END //

CREATE FUNCTION editMouse(userId INT UNSIGNED, mouseId INT UNSIGNED, gender TINYINT, genotypeId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        UPDATE mice
            SET sex = gender, genotype_id = genotypeId
            WHERE mouse_id = mouseId
                AND creator_id = userId;

        RETURN ROW_COUNT();
    END //

CREATE FUNCTION mouseDeceased(userId INT UNSIGNED, mouseId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        UPDATE mice
            SET time_deceased = CURRENT_TIMESTAMP, cage_id = NULL
            WHERE mouse_id = mouseId
                AND creator_id = userId;

        RETURN ROW_COUNT();
    END //

CREATE FUNCTION getGender(userId INT UNSIGNED, mouseId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        DECLARE result INT DEFAULT NULL;
        SELECT sex
            INTO result
            FROM mice
            WHERE creator_id = userId AND mouse_id = mouseId;

        RETURN result;
    END //
CREATE FUNCTION moveMouseToCage(userId INT UNSIGNED, mouseId INT UNSIGNED, cageId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        UPDATE mice
            SET cage_id = cageId
            WHERE mouse_id = mouseId
                AND creator_id = userId;

        RETURN ROW_COUNT();
    END //

CREATE PROCEDURE getCage(userId INT UNSIGNED, cageId INT UNSIGNED)
    BEGIN
        SELECT
            cage_id,
            time_created,
            cage_name
        FROM cages
        WHERE creator_id = userId
            AND cage_id = cageId;
    END //

CREATE PROCEDURE getCages(userId INT UNSIGNED)
    BEGIN
        SELECT
            cage_id,
            time_created,
            cage_name
        FROM cages
        WHERE creator_id = userId;
    END //

CREATE PROCEDURE getCagesWithoutGender(userId INT UNSIGNED, gender TINYINT)
    BEGIN
        SELECT
            cages.cage_id,
            cages.time_created,
            cages.cage_name
        FROM cages
        LEFT JOIN mice
            ON mice.cage_id = cages.cage_id
        WHERE cages.creator_id = userId
            AND (
                mice.sex != gender
                OR
                mice.sex IS NULL
            )
        GROUP BY cages.cage_id;
    END //

CREATE PROCEDURE getCageOccupants(userId INT UNSIGNED, cageId INT UNSIGNED)
    BEGIN
        SELECT
            mouse_id,
            sex,
            litter_id,
            genotype_id,
            time_created,
            time_deceased
        FROM mice
        WHERE creator_id = userId
            AND cage_id = cageId;
    END //

CREATE PROCEDURE getAllMice(userId INT UNSIGNED)
    BEGIN
        SELECT
            mouse_id,
            sex,
            mice.litter_id,
            genotypes.genotype_id,
            mice.time_created,
            time_deceased,
            litters.mother_id,
            litters.father_id,
            litters.birth_date,
            genotypes.genotype_name,
            genotypes.genotype_color
        FROM mice
        LEFT JOIN litters
            on mice.litter_id = litters.litter_id
        LEFT JOIN genotypes
            on mice.genotype_id = genotypes.genotype_id
        WHERE mice.creator_id = userId
        GROUP BY mice.mouse_id;
    END //

CREATE PROCEDURE getGenderFromCage(userId INT UNSIGNED, cageId INT UNSIGNED, gender TINYINT)
    BEGIN
        SELECT
            mouse_id,
            sex,
            litter_id,
            genotype_id,
            time_created,
            time_deceased
        FROM mice
        WHERE creator_id = userId
            AND cage_id = cageId
            AND sex = gender;
    END //

CREATE PROCEDURE getMouse(userId INT UNSIGNED, mouseId INT UNSIGNED)
    BEGIN
        SELECT
            mouse_id,
            sex,
            mice.litter_id,
            genotypes.genotype_id,
            mice.time_created,
            time_deceased,
            litters.mother_id,
            litters.father_id,
            litters.birth_date,
            genotypes.genotype_name,
            genotypes.genotype_color
        FROM mice
        LEFT JOIN litters
            on mice.litter_id = litters.litter_id
        LEFT JOIN genotypes
            on mice.genotype_id = genotypes.genotype_id
        WHERE mice.creator_id = userId
            AND mice.mouse_id = mouseId
        GROUP BY mice.mouse_id;
    END //

CREATE PROCEDURE getGenotypes(userId INT UNSIGNED)
    BEGIN
        SELECT
            genotype_id,
            genotype_name
        FROM genotypes
        WHERE creator_id = userId;
    END //
DELIMITER ;