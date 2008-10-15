/* ajout d'un statut dans les session : 'archivée' */
UPDATE `Permission` SET `DescrPermis` = 'Peut changer le statut de n''importe quelle session (ouverte,fermée,invisible,archivée)' WHERE `permission`.`IdPermission` =12 LIMIT 1 ;
UPDATE `Permission` SET `DescrPermis` = 'Peut changer le statut de "sa" session (ouverte,fermée,invisible,archivée)' WHERE `permission`.`IdPermission` =13 LIMIT 1 ;
UPDATE `Permission` SET `DescrPermis` = 'Modère les forum de la session' WHERE `permission`.`IdPermission` =62 LIMIT 1 ;

/* ajout des permissions pour les archives */
INSERT INTO `Permission` (`IdPermission`, `NomPermis`, `DescrPermis`) VALUES (NULL, 'PERM_VOIR_SESSION_ARCHIVES', 'Peut voir et accéder aux formations archivées');
INSERT INTO `Permission` (`IdPermission` ,`NomPermis` ,`DescrPermis`) VALUES (NULL, 'PERM_MOD_SESSION_ARCHIVES', 'Peut modifier une formation archivée');

/* ajout des permissions pour les différents statuts */
/* INSERT INTO `Statut_Permission` (`IdPermission`, `IdStatut`) VALUES ('106', '1'), ('106', '2'), ('106', '3'), ('106', '5'), ('106', '7'); voir les sessions archivées */

