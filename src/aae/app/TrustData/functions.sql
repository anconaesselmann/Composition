DELIMITER //

CREATE PROCEDURE getConnectionTrustScore(userId INT UNSIGNED, connectionId INT UNSIGNED)
	BEGIN
		DECLARE otherId INT DEFAULT NULL;

        SELECT getOtherConnectionIdById(userId, connectionId)
    		INTO otherId;

    	SELECT trust_time_final, trust_time_given, trust_time_gotten
			FROM user_trust_time
			WHERE user_id = otherId;
	END //

CREATE FUNCTION prepareTrustScoreUpdate()
    RETURNS BOOLEAN
    BEGIN
        DECLARE result BOOLEAN DEFAULT FALSE;

        DECLARE lastCalculated TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        DECLARE allowUpdateAfterSeconds INT UNSIGNED DEFAULT 1800;

        DECLARE secDiff INT UNSIGNED DEFAULT 0;

        SELECT last_calculated, allow_update_after_seconds
            INTO lastCalculated, allowUpdateAfterSeconds
            FROM trust_settings
            WHERE id = 1;

        SELECT TIMESTAMPDIFF(SECOND, lastCalculated, CURRENT_TIMESTAMP)
            INTO secDiff;

        IF secDiff >= allowUpdateAfterSeconds THEN
            UPDATE user_trust_time
                SET temp_trust_time_gotten = 0,
                    temp_trust_time_given  = 0;
            UPDATE trust_settings
                SET last_calculated = CURRENT_TIMESTAMP;
            SET result = TRUE;
        END IF;

        RETURN result;
    END //

CREATE FUNCTION finalizeTrustScoreUpdate()
    RETURNS BOOLEAN
    BEGIN
        DECLARE result BOOLEAN DEFAULT FALSE;
        DECLARE tsNow TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        UPDATE user_trust_time
            SET trust_time_given       = temp_trust_time_given,
                trust_time_gotten      = temp_trust_time_gotten,
                temp_trust_time_given  = 0,
                temp_trust_time_gotten = 0,
                trust_time_final       =
                    CASE
                        WHEN trust_time_given < trust_time_gotten
                        THEN trust_time_given
                        ELSE trust_time_gotten
                    END,
                last_calculated = tsNow;

        RETURN result;
    END //

CREATE PROCEDURE getUserIds()
    BEGIN
        SELECT user_id
            FROM users;
    END //

CREATE PROCEDURE getAllConnectionIds(userId INT)
    BEGIN
        SELECT connection_id
            FROM connections
            WHERE user_a_id = userId
                OR user_b_id = userId;
    END //

CREATE FUNCTION getPersonalScore(userId INT)
    RETURNS INT
    BEGIN
        DECLARE result INT DEFAULT 0;
        SELECT trust_time_final
            INTO result
            FROM user_trust_time
            WHERE user_id = userId;
        RETURN result;
    END //

CREATE FUNCTION getNbrPointsInvested(userId INT, connectionId INT)
    RETURNS INT
    BEGIN
        DECLARE result INT DEFAULT 0;
        SELECT points_invested
            INTO result
            FROM connection_trust
            WHERE connection_id = connectionId
                AND giver_id = userId;
        RETURN result;
    END //

CREATE FUNCTION updateConnectionTrustTime(userId INT, connectionId INT, nowTimestamp TIMESTAMP, maxTimeGiven BIGINT UNSIGNED)
    RETURNS BIGINT UNSIGNED
    BEGIN
        DECLARE result  INT DEFAULT 0;
        DECLARE otherId INT DEFAULT NULL;
        DECLARE tsThen  TIMESTAMP DEFAULT NULL;
        DECLARE secDiff INT DEFAULT 0;
        DECLARE totalTrustTime BIGINT UNSIGNED DEFAULT 0;
        DECLARE newTotalTrustTime BIGINT UNSIGNED DEFAULT 0;

        SELECT last_calculated, total_trust_time
            INTO tsThen, totalTrustTime
            FROM connection_trust
            WHERE connection_id = connectionId
                AND giver_id = userId;

        IF tsThen IS NOT NULL THEN
            SELECT TIMESTAMPDIFF(SECOND, tsThen, nowTimestamp)
                INTO secDiff;

            SELECT getOtherConnectionIdById(userId, connectionId)
            	INTO otherId;

            UPDATE connection_trust
                SET last_calculated = nowTimestamp
                WHERE connection_id = connectionId
                    AND giver_id = userId;

            set newTotalTrustTime = totalTrustTime + secDiff;

            IF newTotalTrustTime > maxTimeGiven THEN
                SET newTotalTrustTime = maxTimeGiven;
            END IF;

            UPDATE connection_trust
                SET total_trust_time = newTotalTrustTime
                WHERE connection_id = connectionId
                    AND giver_id = userId;

            UPDATE user_trust_time
                SET temp_trust_time_given = temp_trust_time_given + newTotalTrustTime
                WHERE user_id = userId;

            UPDATE user_trust_time
                SET temp_trust_time_gotten = temp_trust_time_gotten + newTotalTrustTime
                WHERE user_id = otherId;

            SET result = newTotalTrustTime;
        END IF;

        RETURN result;
    END //
/*
    Adds a trust point to the user in the connection that is not userId
 */
CREATE FUNCTION addTrustPointsToConnection(userId INT, connectionId INT, points INT)
    RETURNS INT
    BEGIN
        DECLARE pointsInvested INT DEFAULT NULL;
        DECLARE otherId INT DEFAULT NULL;

        SELECT points_invested
            INTO pointsInvested
            FROM connection_trust
            WHERE connection_id = connectionId
                AND giver_id = userId;

        IF pointsInvested IS NULL THEN
            INSERT INTO connection_trust
                VALUES (connectionId, userId, points, 0, CURRENT_TIMESTAMP);
            SELECT getOtherConnectionIdById(userId, connectionId)
        		INTO otherId;
            INSERT IGNORE INTO user_trust_time
            	VALUES(userId,0,0,0,0,0, CURRENT_TIMESTAMP);
            INSERT IGNORE INTO user_trust_time
            	VALUES(otherId,0,0,0,0,0, CURRENT_TIMESTAMP);

            SET pointsInvested = points;
        ELSE
            SET pointsInvested = pointsInvested + points;
            UPDATE connection_trust
                SET points_invested = pointsInvested
                WHERE connection_id = connectionId
                    AND giver_id = userId;
        END IF;
        RETURN pointsInvested;
    END //

CREATE FUNCTION subtractTrustPointsToConnection(userId INT, connectionId INT, points INT)
    RETURNS INT
    BEGIN
        DECLARE pointsInvested INT DEFAULT NULL;
        SELECT points_invested
            INTO pointsInvested
            FROM connection_trust
            WHERE connection_id = connectionId
                AND giver_id = userId;

        IF pointsInvested IS NULL THEN
            INSERT INTO connection_trust
                VALUES (connectionId, userId, 0, 0, NOW());
            SET pointsInvested = 0;
        ELSE
            SET pointsInvested = pointsInvested - points;
            IF pointsInvested < 0 THEN
                SET pointsInvested = 0;
            END IF;
            UPDATE connection_trust
                SET points_invested = pointsInvested
                WHERE connection_id = connectionId
                    AND giver_id = userId;
        END IF;
        RETURN pointsInvested;
    END //
DELIMITER ;