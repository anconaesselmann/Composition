DELIMITER //

CREATE PROCEDURE getUserDetails(userId INT)
	BEGIN
		SELECT
				first_name,
				last_name,
                phone_nbr ,
                address,
                city,
                zip,
                state,
                country
			FROM user_details WHERE user_id = userId;
	END //
CREATE FUNCTION updateUserDetails(
        _userId    INT,
        _firstName VARCHAR(255),
        _lastName  VARCHAR(255),
        _phoneNbr  VARCHAR(255),
        _address   VARCHAR(255),
        _city      VARCHAR(255),
        _zip       VARCHAR(255),
        _state     VARCHAR(255),
        _country   VARCHAR(255)
    )
    RETURNS INT
    BEGIN
        UPDATE user_details
            SET first_name = COALESCE(_firstName, first_name),
                last_name  = COALESCE(_lastName, last_name),
                phone_nbr  = COALESCE(_phoneNbr, phone_nbr),
                address    = COALESCE(_address, address),
                city       = COALESCE(_city, city),
                zip        = COALESCE(_zip, zip),
                state      = COALESCE(_state, state),
                country    = COALESCE(_country, country)
            WHERE user_id = _userId;

        RETURN ROW_COUNT();
    END //
CREATE PROCEDURE get_all_persistent_logins(userEmail VARCHAR(255))
	BEGIN
		CALL set_userId(userEmail);
		SELECT device_name, browser_info
			FROM persistent_logins
			WHERE user_id = @_userId;
	END //
CREATE FUNCTION get_persistent_login(deviceName VARCHAR(255), userEmail VARCHAR(255))
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE result BOOLEAN DEFAULT FALSE;
		DECLARE code   CHAR(255) DEFAULT NULL;

		CALL set_userId(userEmail);

		SELECT rand_code_hash
			INTO code
			FROM persistent_logins
			WHERE user_id = @_userId AND device_name = deviceName;

		RETURN code;
	END //

CREATE FUNCTION remove_all_persistent_logins(userEmail VARCHAR(255))
	RETURNS INT
	BEGIN
		CALL set_userId(userEmail);
		DELETE FROM persistent_logins
			WHERE user_id = @_userId;
		RETURN ROW_COUNT();
	END //

CREATE FUNCTION unset_persistent_login(userEmail VARCHAR(255), deviceName VARCHAR(255))
	RETURNS INT
	BEGIN
		CALL set_userId(userEmail);
		DELETE FROM persistent_logins
			WHERE user_id = @_userId AND device_name = deviceName;
		RETURN ROW_COUNT();
	END //

CREATE FUNCTION set_persistent_login(pwHash VARCHAR(255), userEmail VARCHAR(255), deviceName VARCHAR(255), browserInfo BLOB)
	RETURNS BOOLEAN
	BEGIN
		DECLARE seriesID INT UNSIGNED DEFAULT NULL;
		DECLARE result BOOLEAN DEFAULT FALSE;

		CALL set_userId(userEmail);

		SELECT series_id
			INTO seriesID
			FROM persistent_logins
			where user_id = @_userId AND deviceName = device_name;

		IF seriesID IS NULL THEN
			INSERT INTO persistent_logins
				VALUES (NULL, @_userId, deviceName, browserInfo, pwHash);
			SET result = TRUE;
		ELSE
			UPDATE persistent_logins
				SET rand_code_hash = pwHash, browser_info = browserInfo
					WHERE series_id   = seriesID;
			SET result = TRUE;
		END IF;

		RETURN result;
	END //

/* the password should be created with php's password_hash function. The code parameter is used to later verify the users email. */
CREATE FUNCTION createUser(userName VARCHAR(255), userPassword VARCHAR(255), userEmail VARCHAR(255), code CHAR(255), remote_addr VARCHAR(45))
	RETURNS INT
	BEGIN
		DECLARE userId INT DEFAULT NULL;
		INSERT INTO users
			VALUES(NULL, userName, userPassword, userEmail, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, DEFAULT);
		SET userId = LAST_INSERT_ID();
		INSERT INTO reset_codes
			VALUES(NULL, userId, code, DEFAULT, remote_addr);
		RETURN userId;
	END //

CREATE FUNCTION verifyEmail(email VARCHAR(255), code CHAR(255))
	RETURNS BOOLEAN
	BEGIN
		DECLARE result BOOLEAN DEFAULT FALSE;
		DECLARE userStatus TINYINT DEFAULT 0;
		DECLARE userId INT DEFAULT 0;
		DECLARE dbCode CHAR(255) DEFAULT NULL;

		SELECT user_status, user_id
			INTO userStatus, userId
			FROM users
			where user_email = email;

		SELECT reset_code
			INTO dbCode
			FROM reset_codes
			WHERE user_id = userId;

		IF code IS NOT NULL THEN
			IF dbCode = code THEN
				UPDATE users
					SET user_status = 1
					WHERE user_id = userId;
				# remove all reset codes
				DELETE FROM reset_codes
					WHERE user_id = userId;
				SET result = TRUE;
			END IF;
		END IF;

		RETURN result;
	END //

CREATE FUNCTION getUserName(userId INT)
	RETURNS CHAR(255)
	BEGIN
		DECLARE userName CHAR(255) DEFAULT NULL;
		SELECT user_name
			INTO userName
			FROM users
			WHERE user_id = userId;
		RETURN userName;
	END //

CREATE PROCEDURE getUserById(IN userId INT)
	BEGIN
		SELECT user_id, user_name, user_email, user_created
			FROM users
			WHERE user_id = userId;
	END //

CREATE PROCEDURE getUserByEmail(IN userEmail VARCHAR(255))
	BEGIN
		SELECT user_id, user_name, user_email, user_created
			FROM users
			WHERE user_email = userEmail;
	END //

# TODO: only allow resets on users that are verified
CREATE FUNCTION resetPasswordWithId(userId INT, newPassword VARCHAR(255), resetCode VARCHAR(255))
	RETURNS BOOLEAN
	BEGIN
		DECLARE resetDate          TIMESTAMP     DEFAULT NULL;
		DECLARE retrievedResetCode CHAR(255)     DEFAULT NULL;
		DECLARE remoteAddr         VARCHAR(45)   DEFAULT NULL;
		DECLARE oldPassword        VARCHAR(255)  DEFAULT NULL;
		DECLARE result             BOOLEAN       DEFAULT FALSE;
		DECLARE secResetValid      INT           DEFAULT 100;

		IF userId IS NOT NULL THEN
			SELECT reset_time, reset_code, remote_addr
				INTO resetDate, retrievedResetCode, remoteAddr
				FROM reset_codes
				WHERE user_id = userId
				ORDER BY reset_time DESC
				LIMIT 0,1;
			IF retrievedResetCode IS NOT NULL
				AND retrievedResetCode = resetCode
			THEN
				SELECT seconds_reset_codes_valid
					INTO secResetValid
					FROM application_settings
					WHERE application_id = 1;

				IF _timestampWhitinSeconds(resetDate, secResetValid) IS TRUE THEN
					SET result = TRUE;
					# record old password in old_passwords table
					SELECT user_password
						INTO oldPassword
						FROM users
						WHERE user_id = userId;
					INSERT INTO old_passwords
						VALUES(NULL, userId, oldPassword, CURRENT_TIMESTAMP, remoteAddr);
					# update the password
					UPDATE users
						SET user_password = newPassword,
						    password_age  = CURRENT_TIMESTAMP
						WHERE user_id = userId;
				END IF;
				# remove all reset codes, even on failed reset attempts
				DELETE FROM reset_codes
					WHERE user_id = userId;
			END IF;
		END IF;
		RETURN result;
	END //

CREATE FUNCTION resetPassword(email VARCHAR(255), newPassword VARCHAR(255), resetCode VARCHAR(255))
	RETURNS BOOLEAN
	BEGIN
		DECLARE userId INT UNSIGNED DEFAULT NULL;

		SELECT user_id
			INTO userId
			FROM users
			WHERE users.user_email = email;

		RETURN resetPasswordWithId(userId, newPassword, resetCode);
	END //

CREATE FUNCTION requestPasswordReset(email VARCHAR(255), resetCode CHAR(255), remoteAddr VARCHAR(45))
	RETURNS BOOLEAN
	BEGIN
		DECLARE userId INT UNSIGNED DEFAULT NULL;

		SELECT user_id
			INTO userId
			FROM users
			WHERE user_email = email;

		INSERT
			INTO reset_codes
			VALUES (NULL, userId , resetCode, CURRENT_TIMESTAMP, remoteAddr);

		RETURN TRUE;
	END//

CREATE FUNCTION _timestampWhitinSeconds(ts TIMESTAMP, seconds INT)
	RETURNS BOOLEAN
	BEGIN
		SET @result_timestampWhitinSeconds = FALSE;
		SET @secondsDiff = 0;

		SELECT
			UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(ts)
			INTO @secondsDiff;

		IF @secondsDiff > seconds THEN
			SET @result_timestampWhitinSeconds = FALSE;
		ELSE
			SET @result_timestampWhitinSeconds = TRUE;
		END IF;
		RETURN @result_timestampWhitinSeconds;
	END //

CREATE FUNCTION _acceptLogin(email VARCHAR(255))
	RETURNS BOOLEAN
	BEGIN
		DECLARE result      BOOLEAN      DEFAULT FALSE;
		DECLARE lastTime    TIMESTAMP    DEFAULT NULL;
		DECLARE lastSuccess BOOLEAN      DEFAULT NULL;
		DECLARE tooSoon     BOOLEAN      DEFAULT NULL;
		DECLARE userStatus  TINYINT      DEFAULT 0;
		DECLARE userId      INT UNSIGNED DEFAULT NULL;
		DECLARE secLoginDelay INT UNSIGNED DEFAULT 1;

		#SELECT logins.login_time, logins.login_success
		#	INTO lastTime, lastSuccess
		#	FROM logins
		#	INNER JOIN users ON logins.user_id = users.user_id
		#	WHERE users.user_email = email
		#	ORDER BY logins.login_time DESC
		#	LIMIT 0,1;

		SELECT user_status, user_id
			INTO userStatus, userId
			FROM users
			WHERE user_email = email;

		IF userStatus > 0 THEN

			SELECT login_time, login_success
				INTO lastTime, lastSuccess
				FROM logins
				WHERE user_id = userId
				ORDER BY login_time DESC
				LIMIT 0,1;

			IF lastSuccess = FALSE THEN
				SELECT seconds_between_login_attempts
					INTO secLoginDelay
					FROM application_settings
					WHERE application_id=1;

				SELECT _timestampWhitinSeconds(lastTime, secLoginDelay)
					INTO tooSoon;
				SET result = NOT tooSoon;
			ELSE
				SET result = TRUE;
			END IF;

			/* Successful login means that all reset attempts should be invalidated */
			DELETE FROM reset_codes
				WHERE user_id = userId;

		END IF;

		RETURN result;
	END //

CREATE FUNCTION getPasswordHash(email VARCHAR(255))
	RETURNS CHAR(255)
	BEGIN
		DECLARE userId INT UNSIGNED DEFAULT NULL;
		DECLARE pwHash VARCHAR(255) DEFAULT NULL;

		IF _acceptLogin(email) = TRUE THEN
			SELECT user_id, user_password
				INTO userId, pwHash
				FROM users
				WHERE user_email = email;

		END IF;

	    RETURN pwHash;
	END //

CREATE FUNCTION logLogin(email VARCHAR(255), loginSuccess BOOLEAN, loginType TINYINT, remoteAddr VARCHAR(45))
	RETURNS INT
    BEGIN
    	DECLARE userId  INT UNSIGNED DEFAULT NULL;
    	DECLARE result  INT UNSIGNED DEFAULT 0;

		SELECT user_id
			INTO userId
			FROM users
			WHERE user_email = email;

		IF userId IS NOT NULL THEN
			INSERT INTO logins VALUES(NULL, userId, CURRENT_TIMESTAMP, loginSuccess, loginType, remoteAddr);
			IF loginSuccess = TRUE THEN
				SET result = userId;
			END IF;
		END IF;

	    RETURN result;
    END //

CREATE PROCEDURE raise (errno SMALLINT UNSIGNED, message VARCHAR(256))
	BEGIN
		/*SIGNAL SQLSTATE
		    '11198'
		SET
			MYSQL_ERRNO  = errno,
			MESSAGE_TEXT = message;*/
	END //

CREATE PROCEDURE set_userId(userEmail VARCHAR(255))
	BEGIN
		IF @_userId IS NULL THEN
			SELECT user_id
				INTO @_userId
				FROM users
				WHERE user_email = userEmail;
		END IF;

		IF @_userId IS NULL THEN
			CALL raise(1112, "_userId could not be retrieved");
		END IF;
	END //

# Application Initialization

INSERT INTO application_settings (seconds_between_login_attempts) VALUES (5);
UPDATE application_settings SET seconds_reset_codes_valid = 900 WHERE application_id=1;

