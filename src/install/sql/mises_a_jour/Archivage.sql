/* ajout d'un statut dans les session : 'archiv�e' */
UPDATE `Permission` SET `DescrPermis` = 'Peut changer le statut de n''importe quelle session (ouverte,ferm�e,invisible,archiv�e)' WHERE `permission`.`IdPermission` =12 LIMIT 1 ;
UPDATE `Permission` SET `DescrPermis` = 'Peut changer le statut de "sa" session (ouverte,ferm�e,invisible,archiv�e)' WHERE `permission`.`IdPermission` =13 LIMIT 1 ;
UPDATE `Permission` SET `DescrPermis` = 'Mod�re les forum de la session' WHERE `permission`.`IdPermission` =62 LIMIT 1 ;

/* ajout des permissions pour les archives */
INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_VOIR_SESSION_ARCHIVES', 'Peut voir et acc�der aux formations archiv�es');
INSERT INTO `Permission` (`IdPermission` ,`NomPermis` ,`DescrPermis`) VALUES (NULL, 'PERM_MOD_SESSION_ARCHIVES', 'Peut modifier une formation archiv�e');

/* ajout des permissions pour les diff�rents statuts */
/* INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ('106', '1'), ('106', '2'), ('106', '3'), ('106', '5'), ('106', '7'); voir les sessions archiv�es */

