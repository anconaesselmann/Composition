DELIMITER //

CREATE PROCEDURE get_runkeeper_login(IN userId INT UNSIGNED)
    BEGIN
        SELECT user_name, user_email, user_password
            FROM runkeeper_login
            WHERE user_id = userId;
    END //

CREATE FUNCTION getMostRecentRunkeeperActivityId (userId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        DECLARE result INT UNSIGNED;

        SELECT last_logged_activity_id
            INTO result
            FROM runkeeper_sync
            WHERE user_id = userId;

        RETURN result;
    END //

CREATE FUNCTION updateMostRecentRunkeeperActivityId (userId INT UNSIGNED, activityId INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        INSERT INTO runkeeper_sync
            VALUES(userId, NULL, activityId)
            ON DUPLICATE KEY
                UPDATE
                    last_logged_activity_id = activityId,
                    last_logged_date = NULL;

        RETURN ROW_COUNT();
    END //

CREATE FUNCTION _createActivity (userId INT UNSIGNED, activityId INT UNSIGNED, activityType VARCHAR(64), activityDate TIMESTAMP)
    RETURNS BOOLEAN
    BEGIN
        DECLARE result BOOLEAN DEFAULT FALSE;
        DECLARE type TINYINT DEFAULT 0;


        DECLARE activityExists BOOLEAN DEFAULT FALSE;

        SELECT EXISTS(SELECT 1 FROM activities WHERE activity_id = activityId)
            INTO activityExists;

        IF activityExists = FALSE THEN
            CASE activityType
                WHEN "Running" THEN SET type = 1;
                WHEN "Cycling" THEN SET type = 2;
                WHEN "Hiking" THEN  SET type = 3;
                WHEN "Walking" THEN SET type = 4;
                ELSE SET type = 0;
            END CASE;

            INSERT INTO activities
                VALUES(activityId, userId, activityDate, type, CURRENT_TIMESTAMP);

            SET result = TRUE;
        END IF;


        RETURN result;
    END //

CREATE FUNCTION insertTrackPoint (userId INT UNSIGNED, activityId INT UNSIGNED, lat DOUBLE, lon DOUBLE, elev DOUBLE, t TIMESTAMP, activityType VARCHAR(64), sequenceNbr INT UNSIGNED, gridId INT UNSIGNED, gridLevel INT UNSIGNED)
    RETURNS INT UNSIGNED
    BEGIN
        DECLARE trackExists INT UNSIGNED;
        DECLARE result INT UNSIGNED;
        DECLARE pointId INT UNSIGNED;
        DECLARE sequenceId INT UNSIGNED;
        DECLARE activityExists BOOLEAN;

        SELECT _createActivity(userId, activityId, activityType, t)
            INTO activityExists;

        SELECT EXISTS(SELECT 1 FROM tracks WHERE track_id = activityId)
            INTO trackExists;

        IF trackExists = 0 THEN
            INSERT INTO tracks
                VALUES (activityId, userId, t, t);
            SET result = 111;
        ELSE
            UPDATE tracks
                SET end_time = t
                    WHERE track_id = activityId;
            SET result = 222;
        END IF;

        SELECT sequence_id
            INTO sequenceId
            FROM sequences
            WHERE track_id = activityId
                AND sequenceNbr = sequence_nbr;

        IF  sequenceId IS NULL THEN
            INSERT INTO sequences
                VALUES (NULL, activityId, sequenceNbr);
            SET result = 333;
            SET sequenceId = LAST_INSERT_ID();
        END IF;

        INSERT INTO points
            VALUES (NULL, lat, lon, elev, t);

        SET pointId = LAST_INSERT_ID();

        INSERT INTO sequence_points
            VALUES(
                pointId, sequenceId
            );

        INSERT INTO grids
            VALUES (
                gridId, pointId, gridLevel
            );

        RETURN result;
    END //

CREATE PROCEDURE getTrack(in activityId INT UNSIGNED, in level INT UNSIGNED)
    BEGIN
        SELECT points.latitude, points.longitude, points.elevation, points.time, grids.level, tracks.track_id, sequences.sequence_nbr
            FROM tracks
            LEFT JOIN sequences
                ON tracks.track_id = sequences.track_id
            LEFT JOIN sequence_points
                ON sequences.sequence_id = sequence_points.sequence_id
            LEFT JOIN points
                ON sequence_points.point_id = points.point_id
            LEFT JOIN grids
                ON grids.point_id = sequence_points.point_id
            WHERE tracks.track_id = activityId
                AND grids.level <= level
            ORDER BY points.point_id;
    END //

CREATE PROCEDURE getRunsUserIds()
    BEGIN
        SELECT user_id
            FROM runkeeper_login;
    END //

CREATE PROCEDURE getActivityIds(in userId INT UNSIGNED)
    BEGIN
        SELECT activity_id FROM activities WHERE user_id = userId AND type = 1;
    END //

DELIMITER ;
