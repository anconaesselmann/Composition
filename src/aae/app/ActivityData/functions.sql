DELIMITER //

-- CREATE PROCEDURE getConnectionsActivity(IN userEmail VARCHAR(255))
--     BEGIN
--         CALL set_userId(userEmail);

--         SELECT
-- 			activity.activity_id,
-- 			'connections' AS category,
-- 			(CASE activity.action WHEN 1 THEN 'initiated' ELSE 'reciprocated' END) AS action,
-- 			(
-- 				CASE activity.user_id
-- 				WHEN conn.user_a_id
-- 				THEN
-- 					(
-- 						CASE details_b.show_real_name
-- 						WHEN TRUE
-- 						THEN users_b.user_name /* TODO: Change once real names exist */
-- 						ELSE
-- 						(
-- 							CASE details_b.show_user_name
-- 							WHEN TRUE
-- 							THEN users_b.user_name
-- 							ELSE details_b.alias
-- 							END
-- 						)
-- 						END
-- 					)
-- 				ELSE
-- 					(
-- 						CASE details_a.show_real_name
-- 						WHEN TRUE
-- 						THEN users_a.user_name /* TODO: Change once real names exist */
-- 						ELSE
-- 						(
-- 							CASE details_a.show_user_name
-- 							WHEN TRUE
-- 							THEN users_a.user_name
-- 							ELSE details_a.alias
-- 							END
-- 						)
-- 						END
-- 					)
-- 				END
-- 			) AS actor,
-- 			activity.activity_time AS time
-- 		FROM connections_activity AS activity
-- 		INNER JOIN connections AS conn
-- 			ON conn.connection_id = activity.row_id
-- 		INNER JOIN connection_details AS details_a
-- 			ON conn.connection_id = details_a.connection_id AND conn.user_a_id = details_a.user_id
-- 		INNER JOIN connection_details AS details_b
-- 			ON conn.connection_id = details_b.connection_id AND conn.user_b_id = details_b.user_id
-- 		INNER JOIN users AS users_a
-- 			ON conn.user_a_id = users_a.user_id
-- 		INNER JOIN users AS users_b
-- 			ON conn.user_b_id = users_b.user_id
-- 		WHERE activity.user_id = @_userId;
--     END //

-- CREATE PROCEDURE getMessagesActivity(IN userEmail VARCHAR(255))
--     BEGIN
--         CALL set_userId(userEmail);

--         SELECT
-- 			activity.activity_id,
-- 			'messages' AS category,
-- 			(CASE activity.action WHEN 3 THEN 'sent' ELSE 'received' END) AS action,
-- 			(
-- 				CASE connections.user_a_id
-- 				WHEN activity.user_id
-- 				THEN
-- 					(
-- 						CASE details_b.show_real_name
-- 						WHEN TRUE
-- 						THEN users_b.user_name /* TODO: Change once real names exist */
-- 						ELSE
-- 						(
-- 							CASE details_b.show_user_name
-- 							WHEN TRUE
-- 							THEN users_b.user_name
-- 							ELSE details_b.alias
-- 							END
-- 						)
-- 						END
-- 					)
-- 				ELSE
-- 					(
-- 						CASE details_a.show_real_name
-- 						WHEN TRUE
-- 						THEN users_a.user_name /* TODO: Change once real names exist */
-- 						ELSE
-- 						(
-- 							CASE details_a.show_user_name
-- 							WHEN TRUE
-- 							THEN users_a.user_name
-- 							ELSE details_a.alias
-- 							END
-- 						)
-- 						END
-- 					)
-- 				END
-- 			) AS actor,
-- 			activity.activity_time AS time
-- 		FROM messages_activity AS activity
-- 		INNER JOIN messages
-- 			ON messages.message_id = activity.row_id
-- 		INNER JOIN connections
-- 			ON connections.connection_id = messages.connection_id
-- 		INNER JOIN connection_details AS details_a
-- 			ON connections.connection_id = details_a.connection_id
-- 				AND connections.user_a_id = details_a.user_id
-- 		INNER JOIN connection_details AS details_b
-- 			ON connections.connection_id = details_b.connection_id
-- 				AND connections.user_b_id = details_b.user_id
-- 		INNER JOIN users AS users_a
-- 			ON connections.user_a_id = users_a.user_id
-- 		INNER JOIN users AS users_b
-- 			ON connections.user_b_id = users_b.user_id
-- 		WHERE activity.user_id = @_userId;
--     END //

CREATE PROCEDURE getActivity(IN userEmail VARCHAR(255))
	BEGIN
	CALL set_userId(userEmail);

        SELECT
			activity.activity_id,
			'connections' AS category,
			(CASE activity.action WHEN 1 THEN 'initiated' ELSE 'reciprocated' END) AS action,
			(
				CASE activity.user_id
				WHEN conn.user_a_id
				THEN
					(
						CASE details_b.show_real_name
						WHEN TRUE
						THEN users_b.user_name /* TODO: Change once real names exist */
						ELSE
						(
							CASE details_b.show_user_name
							WHEN TRUE
							THEN users_b.user_name
							ELSE details_b.alias
							END
						)
						END
					)
				ELSE
					(
						CASE details_a.show_real_name
						WHEN TRUE
						THEN users_a.user_name /* TODO: Change once real names exist */
						ELSE
						(
							CASE details_a.show_user_name
							WHEN TRUE
							THEN users_a.user_name
							ELSE details_a.alias
							END
						)
						END
					)
				END
			) AS actor,
			activity.activity_time AS time,
			activity.row_id AS category_id
		FROM connections_activity AS activity
		INNER JOIN connections AS conn
			ON conn.connection_id = activity.row_id
		INNER JOIN connection_details AS details_a
			ON conn.connection_id = details_a.connection_id AND conn.user_a_id = details_a.user_id
		INNER JOIN connection_details AS details_b
			ON conn.connection_id = details_b.connection_id AND conn.user_b_id = details_b.user_id
		INNER JOIN users AS users_a
			ON conn.user_a_id = users_a.user_id
		INNER JOIN users AS users_b
			ON conn.user_b_id = users_b.user_id
		WHERE activity.user_id = @_userId

		UNION

		SELECT
			activity.activity_id,
			'messages' AS category,
			(CASE activity.action WHEN 3 THEN 'sent' ELSE 'received' END) AS action,
			(
				CASE connections.user_a_id
				WHEN activity.user_id
				THEN
					(
						CASE details_b.show_real_name
						WHEN TRUE
						THEN users_b.user_name /* TODO: Change once real names exist */
						ELSE
						(
							CASE details_b.show_user_name
							WHEN TRUE
							THEN users_b.user_name
							ELSE details_b.alias
							END
						)
						END
					)
				ELSE
					(
						CASE details_a.show_real_name
						WHEN TRUE
						THEN users_a.user_name /* TODO: Change once real names exist */
						ELSE
						(
							CASE details_a.show_user_name
							WHEN TRUE
							THEN users_a.user_name
							ELSE details_a.alias
							END
						)
						END
					)
				END
			) AS actor,
			activity.activity_time AS time,
			activity.row_id AS category_id
		FROM messages_activity AS activity
		INNER JOIN messages
			ON messages.message_id = activity.row_id
		INNER JOIN connections
			ON connections.connection_id = messages.connection_id
		INNER JOIN connection_details AS details_a
			ON connections.connection_id = details_a.connection_id
				AND connections.user_a_id = details_a.user_id
		INNER JOIN connection_details AS details_b
			ON connections.connection_id = details_b.connection_id
				AND connections.user_b_id = details_b.user_id
		INNER JOIN users AS users_a
			ON connections.user_a_id = users_a.user_id
		INNER JOIN users AS users_b
			ON connections.user_b_id = users_b.user_id
		WHERE activity.user_id = @_userId

		ORDER BY time desc;
	END //

DELIMITER ;