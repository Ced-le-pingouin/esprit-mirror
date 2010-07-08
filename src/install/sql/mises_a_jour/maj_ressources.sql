ALTER TABLE `Ressource` ADD `IdDeposeur` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `IdPers`;
UPDATE `Ressource` SET `IdDeposeur` = `IdPers`