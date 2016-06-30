/*
    Two users give each other one point
 */

INSERT INTO tests.connections VALUES(1, 1, 2, 0, NULL);

SELECT tests.addTrustPointsToConnection(1, 1, 1);
SELECT tests.addTrustPointsToConnection(2, 1, 1);