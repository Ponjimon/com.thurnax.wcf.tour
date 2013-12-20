/* tables */
DROP TABLE IF EXISTS wcf1_tour;
CREATE TABLE wcf1_tour (
	tourID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	tourName VARCHAR(255) NOT NULL,
	description VARCHAR(255) NOT NULL DEFAULT '',
	objectTypeID INT(10) NOT NULL,
	showPrevButton TINYINT(1) NOT NULL DEFAULT 0,
	
	UNIQUE KEY (tourName),
	KEY (objectTypeID)
);

DROP TABLE IF EXISTS wcf1_tour_step;
CREATE TABLE wcf1_tour_step (
	tourStepID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tourID INT(10) NOT NULL,
	showOrder INT(10) NOT NULL,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	target VARCHAR(255) NOT NULL,
	placement ENUM('top', 'bottom', 'right', 'left') NOT NULL DEFAULT 'right',
	title VARCHAR(255) NOT NULL DEFAULT '',
	content MEDIUMTEXT NOT NULL,
	xOffset INT(10) NOT NULL DEFAULT 0,
	yOffset INT(10) NOT NULL DEFAULT 0,
	url VARCHAR(255) NULL DEFAULT NULL,
	
	UNIQUE KEY (tourID, showOrder)
);

/* foreign keys */
ALTER TABLE wcf1_tour ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_step ADD FOREIGN KEY (tourID) REFERENCES wcf1_tour (tourID) ON DELETE CASCADE;
