/* update wcf1_tour */
ALTER TABLE wcf1_tour ADD COLUMN packageID INT(10) NOT NULL;
ALTER TABLE wcf1_tour MODIFY tourTrigger ENUM('firstSite', 'specificSite', 'manual') NOT NULL DEFAULT 'firstSite';

/* update wcf1_tour_step */
ALTER TABLE wcf1_tour_step ADD COLUMN packageID INT(10) NOT NULL;
ALTER TABLE wcf1_tour_step MODIFY placement ENUM('top', 'bottom', 'left', 'right') NOT NULL DEFAULT 'left';
ALTER TABLE wcf1_tour_step MODIFY xOffset INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_tour_step MODIFY yOffset INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_tour_step ADD COLUMN ctaLabel VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE wcf1_tour_step ADD COLUMN onPrev MEDIUMTEXT NULL DEFAULT NULL;
ALTER TABLE wcf1_tour_step ADD COLUMN onNext MEDIUMTEXT NULL DEFAULT NULL;
ALTER TABLE wcf1_tour_step ADD COLUMN onShow MEDIUMTEXT NULL DEFAULT NULL;
ALTER TABLE wcf1_tour_step ADD COLUMN onCTA MEDIUMTEXT NULL DEFAULT NULL;

ALTER TABLE wcf1_tour ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE wcf1_tour_step ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
