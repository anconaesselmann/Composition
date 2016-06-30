/*
    Multiple users give one user 100 points
 */

INSERT INTO tests.connections VALUES(1, 1, 2, 0, NULL);
INSERT INTO tests.connections VALUES(2, 3, 1, 0, NULL);
INSERT INTO tests.connections VALUES(3, 1, 7, 0, NULL);
INSERT INTO tests.connections VALUES(4, 9, 1, 0, NULL);

-- 4 users add points to user with id 1
SELECT tests.addTrustPointsToConnection(2, 1, 10);
SELECT tests.addTrustPointsToConnection(3, 2, 30);
SELECT tests.addTrustPointsToConnection(7, 3, 50);
SELECT tests.addTrustPointsToConnection(9, 4, 10);

-- user 1 adds enough points to another user to be able to receive 100 points from others
SELECT tests.addTrustPointsToConnection(1, 1, 200);