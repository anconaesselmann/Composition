DELIMITER //

CREATE FUNCTION setArtwork(imageId INT UNSIGNED, imageTitle VARCHAR(255), imageMaterials VARCHAR(255), imageDate TIMESTAMP)
	RETURNS INT UNSIGNED
	BEGIN
		DECLARE result INT UNSIGNED DEFAULT 0;

		INSERT INTO artwork(image_id, title, materials, date_created)
			VALUES(imageId, imageTitle, imageMaterials, imageDate)
			ON DUPLICATE KEY UPDATE
				title = IF(imageTitle IS NOT NULL, imageTitle, title),
				materials = IF(imageMaterials IS NOT NULL, imageMaterials, materials),
				date_created = IF(imageDate IS NOT NULL, imageDate, date_created);

		RETURN ROW_COUNT();
	END //

CREATE FUNCTION getArtworkTitle(imageId INT UNSIGNED)
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE result VARCHAR(255) DEFAULT "";

		SELECT title
			INTO result
			FROM artwork
			WHERE image_id = imageId;

		RETURN result;
	END //
CREATE FUNCTION getArtworkMaterials(imageId INT UNSIGNED)
	RETURNS VARCHAR(255)
	BEGIN
		DECLARE result VARCHAR(255) DEFAULT "";

		SELECT materials
			INTO result
			FROM artwork
			WHERE image_id = imageId;

		RETURN result;
	END //
CREATE FUNCTION getArtworkDate(imageId INT UNSIGNED)
	RETURNS TIMESTAMP
	BEGIN
		DECLARE result TIMESTAMP;

		SELECT date_created
			INTO result
			FROM artwork
			WHERE image_id = imageId;

		RETURN result;
	END //
CREATE PROCEDURE getAll()
	BEGIN
		SELECT * FROM artwork;
	END //

DELIMITER ;