DELIMITER //

CREATE FUNCTION deleteInitiatedContract(userEmail VARCHAR(255))
	RETURNS INT
	BEGIN
		CALL set_userId(userEmail);

		DELETE FROM initiated_contracts
			WHERE initiator_id = @_userId;

		RETURN ROW_COUNT();
	END //

CREATE FUNCTION getInitiatedContractCode(contractId INT)
	RETURNS CHAR(255)
	BEGIN
		DECLARE code CHAR(255) DEFAULT NULL;

		SELECT init_code
			INTO code
			FROM initiated_contracts
			WHERE init_contract_id = contractId;

		RETURN code;

	END //

CREATE FUNCTION reciprocateContract(userEmail VARCHAR(255), initContractId INT, code CHAR(255))
	RETURNS INT
	BEGIN
		DECLARE result INT DEFAULT 0;
		DECLARE initiatorId INT DEFAULT NULL;
		DECLARE contractId INT DEFAULT NULL;

		CALL set_userId(userEmail);

		SELECT contract_id
			INTO contractId
			FROM initiated_contracts
			WHERE init_contract_id = initContractId;

		IF contractId IS NULL THEN
			SELECT initiator_id
				INTO initiatorId
				FROM initiated_contracts
				WHERE init_contract_id = initContractId;

			INSERT INTO contracts
				values(NULL, initiatorId, @_userId, NULL);
			UPDATE initiated_contracts
				SET contract_id = LAST_INSERT_ID()
				WHERE init_contract_id = initContractId
					AND initiator_id = initiatorId;
			SET result = ROW_COUNT();
		END IF;

		RETURN result;

	END //

CREATE PROCEDURE set_sharerId(contractId INT UNSIGNED, requesterId INT UNSIGNED)
	BEGIN
		IF @_sharerId IS NULL THEN
			SELECT contractor_id
				INTO @_sharerId
				FROM contracts
				WHERE contract_id = contractId AND contractee_id = requesterId;

			IF @_sharerId IS NULL THEN
				SELECT contractee_id
					INTO @_sharerId
					FROM contracts
					WHERE contract_id = contractId AND contractor_id = requesterId;
			END IF;
		END IF;

		IF @_sharerId IS NULL THEN
			CALL raise(1111, "_sharerId could not be retrieved");
		END IF;
	END //


CREATE FUNCTION getInfo(contractId INT UNSIGNED, userEmail VARCHAR(255))
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE result VARCHAR(255) DEFAULT NULL;

		CALL set_userId(userEmail);
		CALL set_sharerId(contractId, @_userId);

		SELECT info_name
			INTO result
			FROM exposed_info
			WHERE contract_id = contractId AND user_id = @_sharerId;

		RETURN result;

	END //

CREATE FUNCTION initiateContract(userEmail VARCHAR(255), code CHAR(255))
	RETURNS INT
	BEGIN
		DECLARE result BOOLEAN DEFAULT FALSE;

		CALL set_userId(userEmail);

		INSERT INTO initiated_contracts
			VALUES(NULL, @_userId, code, NULL, NULL);

		RETURN LAST_INSERT_ID();

	END //


DELIMITER ;