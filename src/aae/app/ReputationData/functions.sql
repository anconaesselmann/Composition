DELIMITER //

CREATE FUNCTION registerReputationEvent(userEmail VARCHAR(255), eventType TINYINT, benefactorId INT UNSIGNED)
	RETURNS BOOLEAN
	BEGIN
		CALL set_userId(userEmail);
		INSERT INTO reputation_events
			VALUES(NULL, eventType, @_userId, benefactorId, NULL, NULL);
		RETURN TRUE;
	END //

/**
 * Has to be called after getNewReputationEvents.
 * Updates a users rep and returns the new value.
 * All uncounted reputation events get a current timestamp.
 */
CREATE FUNCTION updateRep(newRep INT)
	RETURNS INT
	BEGIN
		DECLARE result INT DEFAULT 0;
		UPDATE reputation_points
			SET points = points + newRep
			WHERE user_id = @_userId;
		UPDATE reputation_events
			SET last_counted = NOW()
			WHERE beneficiary_id = @_userId;
		SELECT points
			INTO result
			FROM reputation_points
			WHERE user_id = @_userId;
		RETURN result;
	END //

/**
 * Gets all reputation event types of events that have not been counted
 */
CREATE PROCEDURE getNewReputationEvents(IN userEmail VARCHAR(255))
	BEGIN
		CALL set_userId(userEmail);
		SELECT event_type, benefactor_id
			FROM reputation_events
			WHERE beneficiary_id = @_userId AND last_counted IS NULL;
	END //

/**
 * Get a users reputation points
 */
CREATE FUNCTION getRep(userEmail VARCHAR(255))
	RETURNS INT UNSIGNED
	BEGIN
		DECLARE repPoints INT UNSIGNED DEFAULT 0;
		CALL set_userId(userEmail);

		SELECT points
			INTO repPoints
			FROM reputation_points
			WHERE user_id=@_userId;

		RETURN repPoints;
	END //

DELIMITER ;