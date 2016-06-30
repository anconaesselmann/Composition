DROP TRIGGER IF EXISTS after_connections_insert;
DROP TRIGGER IF EXISTS after_messages_insert;
DELIMITER //

CREATE TRIGGER after_connections_insert
    AFTER INSERT ON connections
    FOR EACH ROW BEGIN
        INSERT INTO connections_activity
            SET user_id       = NEW.user_a_id,
                action        = 1,
                row_id        = NEW.connection_id,
                activity_time = NEW.date_created;
        INSERT INTO connections_activity
            SET user_id       = NEW.user_b_id,
                action        = 2,
                row_id        = NEW.connection_id,
                activity_time = NEW.date_created;
    END //
CREATE TRIGGER after_messages_insert
    AFTER INSERT ON messages
    FOR EACH ROW BEGIN
        DECLARE recipientId INT DEFAULT 0;

        SELECT
            (
                CASE NEW.sender_id
                WHEN connections.user_a_id
                THEN connections.user_b_id
                ELSE connections.user_a_id
                END
            )
        INTO recipientId
        FROM connections
        WHERE NEW.connection_id = connections.connection_id
            AND (
                    NEW.sender_id = connections.user_a_id OR
                    NEW.sender_id = connections.user_b_id
                );

        INSERT INTO messages_activity
            SET user_id       = NEW.sender_id,
                action        = 3,
                row_id        = NEW.message_id,
                activity_time = NEW.time_sent;
        INSERT INTO messages_activity
            SET user_id       = recipientId,
                action        = 4,
                row_id        = NEW.message_id,
                activity_time = NEW.time_sent;
    END //

DELIMITER ;