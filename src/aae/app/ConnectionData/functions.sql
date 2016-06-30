DELIMITER //

CREATE FUNCTION deleteConnection(userEmail VARCHAR(255), connectionId INT)
	RETURNS BOOLEAN
	BEGIN
		DECLARE result BOOLEAN DEFAULT FALSE;
		DECLARE userAId INT DEFAULT 0;
		DECLARE userBId INT DEFAULT 0;
		CALL set_userId(userEmail);

		SELECT user_a_id, user_b_id
			INTO userAId, userBId
			FROM connections
			WHERE connection_id = connectionId
				AND (
					user_a_id = @_userId OR
					user_b_id = @_userId
				);

		IF userAId != 0 AND userBId != 0 THEN
			INSERT INTO deleted_connections
				SELECT *, NOW()
				FROM connections
				WHERE connection_id = connectionId;
			INSERT INTO deleted_connection_details
				SELECT *
				FROM connection_details
				WHERE connection_id = connectionId
					AND user_id = userAId;
			INSERT INTO deleted_connection_details
				SELECT *
				FROM connection_details
				WHERE connection_id = connectionId
					AND user_id = userBId;

			DELETE FROM connection_details
				WHERE connection_id = connectionId
					AND user_id = userAId;
			DELETE FROM connection_details
				WHERE connection_id = connectionId
					AND user_id = userBId;
			DELETE FROM connections
				WHERE connection_id = connectionId;
			SET result = TRUE;
		END IF;

		RETURN result;
	END //

CREATE FUNCTION checkInitResponse(userEmail VARCHAR(255), initConnectionId INT)
	RETURNS INT
	BEGIN
		DECLARE result INT DEFAULT 0;
		DECLARE connectionId INT DEFAULT NULL;
		CALL set_userId(userEmail);

		SELECT connection_id
			INTO connectionId
			FROM initiated_connections
			WHERE initiator_id = @_userId;

		IF connectionId IS NOT NULL THEN
			DELETE FROM initiated_connections
				WHERE initiator_id = @_userId
					AND connection_id = connectionId;
			SET result = connectionId;
		END IF;
		RETURN result;
	END //
CREATE PROCEDURE getAllConnections(IN userEmail VARCHAR(255))
	BEGIN
		CALL set_userId(userEmail);

		SELECT
			conn.connection_id,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.alias           ELSE details_a.alias           END) AS alias,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.can_be_messaged ELSE details_a.can_be_messaged END) AS can_be_messaged,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_real_name  ELSE details_a.show_real_name  END) AS show_real_name,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_user_name  ELSE details_a.show_user_name  END) AS show_user_name,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_alias      ELSE details_a.show_alias      END) AS show_alias,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_email      ELSE details_a.show_email      END) AS show_email,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_phone      ELSE details_a.show_phone      END) AS show_phone,
			(CASE conn.user_a_id WHEN @_userId THEN details_b.show_address    ELSE details_a.show_address    END) AS show_address,
			(CASE conn.user_a_id WHEN @_userId THEN users_b.user_name         ELSE users_a.user_name         END) AS user_name,
			(CASE conn.user_a_id WHEN @_userId THEN users_b.user_email        ELSE users_a.user_email        END) AS user_email
		FROM connections AS conn
		INNER JOIN connection_details details_a
			ON conn.connection_id = details_a.connection_id AND conn.user_a_id = details_a.user_id
		INNER JOIN connection_details details_b
			ON conn.connection_id = details_b.connection_id AND conn.user_b_id = details_b.user_id
		INNER JOIN users AS users_a
			ON conn.user_a_id = users_a.user_id
		INNER JOIN users AS users_b
			ON conn.user_b_id = users_b.user_id
		WHERE conn.user_a_id = @_userId
			OR conn.user_b_id = @_userId;
	END //

CREATE FUNCTION getOwnDisplayNameForConnection(userEmail VARCHAR(255), connectionId INT)
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE allowed BOOLEAN DEFAULT FALSE;
		DECLARE result VARCHAR(255) DEFAULT "DATA_CORRUPT";

		CALL set_userId(userEmail);

		SELECT show_alias
			INTO allowed
			FROM connection_details
			WHERE user_id = @_userId
				AND connection_id = connectionId;

		IF allowed = TRUE THEN
			SELECT alias
				INTO result
				FROM connection_details
				WHERE user_id = @_userId
				AND connection_id = connectionId;
		ELSE
			SELECT show_user_name
				INTO allowed
				FROM connection_details
				WHERE user_id = @_userId
					AND connection_id = connectionId;

			IF allowed = TRUE THEN
				SELECT user_name
					INTO result
					FROM users
					WHERE user_id = @_userId;
			ELSE
				SELECT show_real_name
					INTO allowed
					FROM connection_details
					WHERE user_id = @_userId
						AND connection_id = connectionId;

					IF allowed = TRUE THEN
						-- TODO: make this work properly
						SET result = "REAL NAME PLACEHOLDER";
						END IF;
			END IF;
		END IF;
		RETURN result;
	END //

CREATE FUNCTION getOtherConnectionIdById(userId INT, connectionId INT)
	RETURNS INT
	BEGIN
		DECLARE otherId INT DEFAULT NULL;
		SET @_userId = userId;

		SELECT user_a_id
			INTO otherId
			FROM connections
			WHERE connection_id = connectionId
				AND user_b_id = @_userId;

		IF otherId IS NULL THEN
			SELECT user_b_id
			INTO otherId
			FROM connections
			WHERE connection_id = connectionId
				AND user_a_id = @_userId;
		END IF;
		RETURN otherId;
	END //

CREATE FUNCTION getOtherConnectionId(userEmail VARCHAR(255), connectionId INT)
	RETURNS INT
	BEGIN
		DECLARE otherId INT DEFAULT NULL;
		CALL set_userId(userEmail);

		SELECT getOtherConnectionIdById(@_userId, connectionId)
			INTO otherId;

		RETURN otherId;
	END //

CREATE PROCEDURE getDetails(IN id INT, IN connectionId INT)
	BEGIN
		SELECT  user_a_id,
                user_b_id,
                status,
                alias,
                can_be_messaged,
                show_real_name,
                show_user_name,
                show_alias,
                show_email,
                show_phone,
                show_address,
                user_name,
				user_email,
				conn.connection_id
			FROM connections AS conn
			INNER JOIN connection_details AS details
				ON conn.connection_id = details.connection_id
			INNER JOIN users AS u
				ON details.user_id = u.user_id
			WHERE conn.connection_id = connectionId
				AND (details.user_id = id);
	END //

CREATE PROCEDURE getOwnDetails(IN userEmail VARCHAR(255), IN connectionId INT)
	BEGIN
		CALL set_userId(userEmail);
		CALL getDetails(@_userId, connectionId);
	END //

CREATE PROCEDURE getOtherDetails(IN userEmail VARCHAR(255), IN connectionId INT)
	BEGIN
		DECLARE otherId INT DEFAULT NULL;

		SELECT getOtherConnectionId(userEmail, connectionId)
			INTO otherId;

		CALL getDetails(otherId, connectionId);
	END //

CREATE FUNCTION getInitiatorConnectionDetails(initConnectionId INT)
	RETURNS TEXT
	BEGIN
		DECLARE result TEXT DEFAULT "";

		SELECT connection_details
			INTO result
			FROM initiated_connections
			WHERE init_connection_id = initConnectionId;
		RETURN result;
	END //

CREATE FUNCTION insertConnectionDetails(
		dataFromInitiator BOOLEAN,
		connectionId INT,
		userEmail VARCHAR(255),
		alias VARCHAR(255),
		canBeMessaged BOOLEAN,
		showRealName BOOLEAN,
		showUserName BOOLEAN,
		showAlias BOOLEAN,
		showEmail BOOLEAN,
		showPhone BOOLEAN,
		showAddress BOOLEAN
	)
	RETURNS INT
	BEGIN
		DECLARE ownerId INT DEFAULT 0;
		CALL set_userId(userEmail);

		IF dataFromInitiator IS TRUE THEN
			SELECT user_a_id
				INTO ownerId
				FROM connections
				WHERE connection_id = connectionId;
		ELSE
			SET ownerId = @_userId;
		END IF;

		INSERT INTO connection_details
			VALUES(
				connectionId,
				ownerId,
				alias,
				canBeMessaged,
				showRealName,
				showUserName,
				showAlias,
				showEmail,
				showPhone,
				showAddress
		);
		RETURN ROW_COUNT();
	END //

CREATE FUNCTION deleteInitiatedConnection(userEmail VARCHAR(255))
	RETURNS INT
	BEGIN
		CALL set_userId(userEmail);

		DELETE FROM initiated_connections
			WHERE initiator_id = @_userId;

		RETURN ROW_COUNT();
	END //

CREATE FUNCTION initiateConnection(userEmail VARCHAR(255), code CHAR(255), connectionDetails TEXT)
	RETURNS INT
	BEGIN
		DECLARE result BOOLEAN DEFAULT FALSE;

		CALL set_userId(userEmail);

		INSERT INTO initiated_connections
			VALUES(NULL, @_userId, code, NULL, connectionDetails, NULL);

		RETURN LAST_INSERT_ID();

	END //

CREATE FUNCTION getInitiatedConnectionCode(connectionId INT)
	RETURNS CHAR(255)
	BEGIN
		DECLARE code CHAR(255) DEFAULT NULL;

		SELECT init_code
			INTO code
			FROM initiated_connections
			WHERE init_connection_id = connectionId;

		RETURN code;

	END //

CREATE FUNCTION reciprocateConnection(userEmail VARCHAR(255), initConnectionId INT, code CHAR(255))
	RETURNS INT
	BEGIN
		DECLARE result INT DEFAULT 0;
		DECLARE initiatorId INT DEFAULT NULL;
		DECLARE connectionId INT DEFAULT NULL;

		CALL set_userId(userEmail);

		SELECT connection_id
			INTO connectionId
			FROM initiated_connections
			WHERE init_connection_id = initConnectionId;

		IF connectionId IS NULL THEN
			SELECT initiator_id
				INTO initiatorId
				FROM initiated_connections
				WHERE init_connection_id = initConnectionId;

			IF initiatorId IS NOT NULL THEN
				INSERT INTO connections
					values(NULL, initiatorId, @_userId, 0, NULL);
				SET connectionId = LAST_INSERT_ID();
				UPDATE initiated_connections
					SET connection_id = connectionId
					WHERE init_connection_id = initConnectionId
						AND initiator_id = initiatorId;
				SET result = connectionId;
			END IF;
		END IF;

		RETURN result;

	END //


DELIMITER ;