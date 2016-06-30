# Application Initialization

INSERT INTO application_settings (seconds_between_login_attempts) VALUES (5);
UPDATE application_settings SET seconds_reset_codes_valid = 900 WHERE application_id=1;

