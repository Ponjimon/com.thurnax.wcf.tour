/* tables */
DROP TABLE IF EXISTS wcf1_tour;
CREATE TABLE wcf1_tour (
	tourID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tourName VARCHAR(255) NOT NULL,
	objectTypeID INT(10) NOT NULL,

	UNIQUE KEY (tourName),
	KEY (objectTypeID)
);

DROP TABLE IF EXISTS wcf1_tour_point;
CREATE TABLE wcf1_tour_point (
	tourPointID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tourID INT(10) NOT NULL,
	objectTypeID INT(10) NOT NULL,
	showOrder INT(10) NOT NULL,
	
	target VARCHAR(255) NULL DEFAULT NULL,
	content MEDIUMTEXT NULL DEFAULT NULL,
	position VARCHAR(255) NULL DEFAULT NULL,
	url VARCHAR(255) NULL DEFAULT NULL,
	
	UNIQUE KEY (tourID, showOrder),
	KEY (objectTypeID)
);

DROP TABLE IF EXISTS wcf1_tour_user;
CREATE TABLE wcf1_tour_user (
	tourID INT(10) NOT NULL,
	userID INT(10) NOT NULL,
	lastTourPointID INT(10) NOT NULL,
	
	UNIQUE KEY (tourID, userID),
	KEY (userID),
	KEY (lastTourPointID)
);

/* foreign keys */
ALTER TABLE wcf1_tour ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_point ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_user ADD FOREIGN KEY (tourID) REFERENCES wcf1_tour (tourID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_user ADD FOREIGN KEY (lastTourPointID) REFERENCES wcf1_tour_point (tourPointID) ON DELETE CASCADE;
