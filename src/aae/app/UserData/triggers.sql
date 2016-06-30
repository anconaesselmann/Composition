DROP TRIGGER IF EXISTS after_users_insert;

DELIMITER //

CREATE TRIGGER after_users_insert
    AFTER INSERT ON users
    FOR EACH ROW BEGIN
        INSERT INTO user_details
            SET user_id = NEW.user_id;
    END //

DELIMITER ;

