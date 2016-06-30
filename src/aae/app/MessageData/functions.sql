DELIMITER //

CREATE FUNCTION getEmailFromConnection(userEmail VARCHAR(255), connectionId INT)
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE otherId INT DEFAULT NULL;
		DECLARE result VARCHAR(255) DEFAULT "";

		SELECT getOtherConnectionId(userEmail, connectionId)
			INTO otherId;

		IF otherId IS NOT NULL THEN

			SELECT user_email
				INTO result
				FROM users
				WHERE user_id = otherId;

		END IF;

		RETURN result;
	END //

CREATE FUNCTION insertMessage(
	senderEmail     VARCHAR(255),
	recipientId     INT,
	messagesSubject VARCHAR(255),
	messagesBody    TEXT,
	sent            BOOLEAN
)
	RETURNS INT
	BEGIN
		DECLARE sentTime TIMESTAMP DEFAULT NULL;
		DECLARE status INT DEFAULT 0;

		IF sent = TRUE THEN
	        SET sentTime = NOW();
	        SET status  = 1;
        END IF;

		CALL set_userId(senderEmail);

		INSERT
			INTO messages
			VALUES(
				NULL,
				@_userId,
				recipientId,
				messagesSubject,
				messagesBody,
				status,
				NOW(),
				sentTime,
				NULL
			);
		RETURN LAST_INSERT_ID();
	END //

CREATE PROCEDURE getMessage(userEmail VARCHAR(255), messageId INT)
	BEGIN
		CALL set_userId(userEmail);

		SELECT
			messages_body as message,
			messages_subject as subject,
			conn.connection_id,
			time_sent,
			time_read
			FROM messages
		INNER JOIN connections AS conn
			ON conn.connection_id = messages.connection_id
		INNER JOIN connection_details AS details_a
			ON conn.connection_id = details_a.connection_id AND conn.user_a_id = details_a.user_id
		INNER JOIN connection_details AS details_b
			ON conn.connection_id = details_b.connection_id AND conn.user_b_id = details_b.user_id
		INNER JOIN users AS users_a
			ON conn.user_a_id = users_a.user_id
		INNER JOIN users AS users_b
			ON conn.user_b_id = users_b.user_id
			WHERE message_id = messageId
				AND (
					conn.user_a_id = @_userId OR
					conn.user_b_id = @_userId
				);
	END //




DELIMITER ;