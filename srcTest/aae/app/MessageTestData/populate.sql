SELECT createUser("axel0",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axelesselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel1",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "a.xelesselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel2",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "ax.elesselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel3",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axe.lesselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel4",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axel.esselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel5",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axele.sselmann@gmail.com", "1234code", NULL);
SELECT createUser("axel6",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axeles.selmann@gmail.com", "1234code", NULL);
SELECT createUser("axel7",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axeless.elmann@gmail.com", "1234code", NULL);
SELECT createUser("axel8",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axelesse.lmann@gmail.com", "1234code", NULL);
SELECT createUser("axel9",  "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axelessel.mann@gmail.com", "1234code", NULL);
SELECT createUser("axel10", "$2y$04$wwBpZ31UZUGrunwuhNPhxORtkPB010ayvVdf103.QgdlvIeScR8ji", "axelesselm.ann@gmail.com", "1234code", NULL);

INSERT INTO connections        VALUES(NULL,9,8,0,"2014-11-12 16:32:34");
INSERT INTO connections        VALUES(NULL,1,9,0,"2014-11-12 16:32:34");
INSERT INTO connections        VALUES(NULL,7,2,0,"2014-11-12 16:32:34");
INSERT INTO connections        VALUES(NULL,1,4,0,"2014-11-12 16:32:34");

INSERT INTO connection_details VALUES(3,2,"tom",0,0,0,1,1,0,0);
INSERT INTO connection_details VALUES(3,7,"tim",1,0,0,0,0,0,0);

INSERT INTO messages VALUES(NULL, 1, 3, "Subject", "This is the body.", 0, "2014-11-12 16:32:34", "2014-11-12 16:32:34", NULL);