/* tables */
DROP TABLE IF EXISTS wcf1_tour;
CREATE TABLE wcf1_tour (
	tourID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	visibleName VARCHAR(255) NOT NULL,
	isDisabled TINYINT(1) NOT NULL DEFAULT 1,
	packageID INT(10) NOT NULL,
	tourTrigger ENUM('firstSite', 'specificSite', 'manual') NOT NULL DEFAULT 'firstSite',
	className VARCHAR(255) NULL DEFAULT NULL,
	identifier VARCHAR(255) NULL DEFAULT NULL,
	
	UNIQUE KEY (identifier),
	KEY (isDisabled)
);

DROP TABLE IF EXISTS wcf1_tour_step;
CREATE TABLE wcf1_tour_step (
	tourStepID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tourID INT(10) NOT NULL,
	showOrder INT(10) NOT NULL,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	packageID INT(10) NOT NULL,
	target VARCHAR(255) NOT NULL,
	orientation ENUM('top-left', 'top-right', 'bottom-left', 'bottom-right') NOT NULL DEFAULT 'top-left',
	content MEDIUMTEXT NOT NULL,
	
	-- optionals
	title VARCHAR(255) NULL DEFAULT NULL,
	xOffset INT(10) NULL DEFAULT NULL,
	yOffset INT(10) NULL DEFAULT NULL,
	url VARCHAR(255) NULL DEFAULT NULL,
	callbackBefore MEDIUMTEXT NULL DEFAULT NULL,
	callbackAfter MEDIUMTEXT NULL DEFAULT NULL,
	
	KEY (tourID)
);

DROP TABLE IF EXISTS wcf1_tour_user;
CREATE TABLE wcf1_tour_user (
	tourID INT(10) NOT NULL,
	userID INT(10) NOT NULL,
	takeTime INT(10) NOT NULL,
	
	UNIQUE KEY (tourID, userID),
	KEY (userID, takeTime)
);

/* foreign keys */
ALTER TABLE wcf1_tour ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_step ADD FOREIGN KEY (tourID) REFERENCES wcf1_tour (tourID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_step ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_user ADD FOREIGN KEY (tourID) REFERENCES wcf1_tour (tourID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
